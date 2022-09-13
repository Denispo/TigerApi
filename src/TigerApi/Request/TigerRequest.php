<?php

namespace TigerApi\Request;

use Nette\Http\IRequest;
use TigerCore\Auth\ICurrentUser;
use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\Request\BaseRequest;
use TigerCore\Request\MatchedRequestData;
use TigerCore\Response\ICanAddPayload;
use TigerCore\Response\NotFoundException;
use TigerCore\Response\UnauthorizedException;
use TigerCore\ValueObject\VO_RouteMask;

abstract class TigerRequest extends BaseRequest {


  abstract protected function onGetMask():VO_RouteMask;
  abstract protected function onSecurityCheck(ICurrentUser $currentUser):RequestSecurityStatus;
  abstract protected function onValidateParams(ICanSetRequestParamIsInvalid $validator);
  abstract protected function onProcessRequest(ICanAddPayload $payload, IRequest $httpRequest):void;


  public function getMask(): VO_RouteMask {
    return $this->onGetMask();
  }

  public function runMatchedRequest(MatchedRequestData $requestData): ICanGetPayloadRawData {
    $securityCheck = $this->onSecurityCheck($requestData->getCurrentUser());
    if (!$securityCheck->IsSetTo(RequestSecurityStatus::REQUEST_ALLOWED)) {
      if ($securityCheck->IsSetTo(RequestSecurityStatus::REQUEST_NOTALLOWED_USER_IS_UNAUTHORIZED)) {
        throw new UnauthorizedException();
      } elseif ($securityCheck->IsSetTo(RequestSecurityStatus::REQUEST_NOTALLOWED_USER_HAS_INSUFFICIENT_RIGHTS)){
        throw new UnauthorizedException();
      }
      throw new NotFoundException();
    }

    $requestParamValidator = new TigerRequestParamValidator();
    $this->onValidateParams($requestParamValidator);
    $invalidParams = $requestParamValidator->getInvalidRequestParams();
    if ($invalidParams) {
      throw new TigerInvalidRequestParamsException($requestParamValidator);
    }

    $this->onProcessRequest($requestData->getPayloadContainer(), $requestData->getHttpRequest());

  }

}