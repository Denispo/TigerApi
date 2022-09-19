<?php

namespace TigerApi\Request;

use TigerCore\Auth\ICurrentUser;

abstract class ATigerPublicRequest extends ATigerBaseRequest {

  protected function onSecurityCheck(ICurrentUser $currentUser):RequestSecurityStatus{
    return new RequestSecurityStatus(RequestSecurityStatus::REQUEST_ALLOWED);
  }

}