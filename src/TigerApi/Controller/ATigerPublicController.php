<?php

namespace TigerApi\Controller;

use TigerApi\Request\RequestSecurityStatus;
use TigerCore\Auth\IAmCurrentUser;

abstract class ATigerPublicController extends ATigerBaseController {

  protected function onSecurityCheck(IAmCurrentUser $currentUser):RequestSecurityStatus{
    return new RequestSecurityStatus(RequestSecurityStatus::REQUEST_ALLOWED);
  }

}