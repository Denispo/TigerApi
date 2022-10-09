<?php

namespace TigerApi\Request;

use Nette\Http\IRequest;
use TigerCore\Auth\IAmCurrentUser;
use TigerCore\Request\BaseRequest;
use TigerCore\Request\MatchedRequestData;
use TigerCore\Response\ICanAddPayload;
use TigerCore\Response\S401_UnauthorizedException;
use TigerCore\Response\S404_NotFoundException;
use TigerCore\ValueObject\VO_RouteMask;

abstract class ATigerBaseRequest extends BaseRequest {


  /**
   * example: '/articles/{id:\d+}[/{title}]'
   * {id} must be a number
   * The /{title} suffix is optional
   * @return VO_RouteMask
   */
  abstract protected function onGetMask():VO_RouteMask;
  abstract protected function onSecurityCheck(IAmCurrentUser $currentUser):RequestSecurityStatus;
  abstract protected function onValidateParams(ICanSetRequestParamIsInvalid $validator);
  abstract protected function onProcessRequest(ICanAddPayload $payload, IRequest $httpRequest):void;


  public function getMask(): VO_RouteMask {
    return $this->onGetMask();
  }

  public function runMatchedRequest(MatchedRequestData $requestData): void {
    $securityCheck = $this->onSecurityCheck($requestData->getCurrentUser());
    if (!$securityCheck->IsSetTo(RequestSecurityStatus::REQUEST_ALLOWED)) {
      if ($securityCheck->IsSetTo(RequestSecurityStatus::REQUEST_NOTALLOWED_USER_IS_UNAUTHORIZED)) {
        throw new S401_UnauthorizedException();
      } elseif ($securityCheck->IsSetTo(RequestSecurityStatus::REQUEST_NOTALLOWED_USER_HAS_INSUFFICIENT_RIGHTS)){
        throw new S401_UnauthorizedException();
      }
      throw new S404_NotFoundException();
    }

    $requestParamValidator = new TigerRequestParamValidator();
    foreach ($requestData->getInvalidParams() as $oneInvalidParam) {
      $requestParamValidator->setRequestParamIsInvalid($oneInvalidParam->getParamName(), $oneInvalidParam->getErrorCode()->getErrorCodeValue());
    }
    $this->onValidateParams($requestParamValidator);
    $invalidParams = $requestParamValidator->getInvalidRequestParams();
    if ($invalidParams) {
      throw new TigerInvalidRequestParamsException($requestParamValidator);
    }

    $this->onProcessRequest($requestData->getPayloadContainer(), $requestData->getHttpRequest());
  }

}