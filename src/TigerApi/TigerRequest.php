<?php

namespace TigerApi;

use Nette\Http\IRequest;
use TigerCore\Auth\ICurrentUser;
use TigerCore\Request\BaseRequest;
use TigerCore\Response\BaseResponseException;
use TigerCore\Response\ICanAddPayload;
use TigerCore\ValueObject\VO_RouteMask;

abstract class TigerRequest extends BaseRequest {


  abstract protected function onGetMask():VO_RouteMask;
  abstract protected function onSecurityCheck(ICurrentUser $currentUser):RequestSecurityCheck;
  abstract protected function onValidateParams(ICanSetRequestParamIsInvalid $paramError);
  abstract protected function onProcessRequest(ICanAddPayload $payload, IRequest $httpRequest):void;


  public function getMask(): VO_RouteMask {
    return $this->onGetMask();
  }

  public function runMatchedRequest(ICurrentUser $currentUser, ICanAddPayload $payload, IRequest $httpRequest): void {
    $securityCheck = $this->onSecurityCheck($currentUser);
    if (!$securityCheck->IsSetTo(RequestSecurityCheck::REQUEST_ALLOWED)) {
      if ($securityCheck->IsSetTo(RequestSecurityCheck::REQUEST_NOTALLOWED_USER_IS_UNAUTHORIZED)) {
        throw new TigerUnauthorizedUserException();
      } elseif ($securityCheck->IsSetTo(RequestSecurityCheck::REQUEST_NOTALLOWED_USER_HAS_INSUFFICIENT_RIGHTS)){
        throw new TigerInsufficientUserRightException();
      }
      throw new BaseResponseException();
    }

    $requestParamValidator = new TigerRequestParamValidator();
    $this->onValidateParams($requestParamValidator);
    $invalidParams = $requestParamValidator->getInvalidRequestParams();
    if ($invalidParams) {
      throw new TigerInvalidRequestParamsException($requestParamValidator);
    }

    $this->onProcessRequest($payload, $httpRequest);

  }

}