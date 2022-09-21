<?php

namespace TigerApi\Request;

use TigerCore\Auth\IAmCurrentUser;

abstract class ATigerPublicRequest extends ATigerBaseRequest {

  protected function onSecurityCheck(IAmCurrentUser $currentUser):RequestSecurityStatus{
    return new RequestSecurityStatus(RequestSecurityStatus::REQUEST_ALLOWED);
  }

}