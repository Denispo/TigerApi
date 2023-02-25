<?php

namespace TigerApi\Controller;

use TigerApi\Request\RequestAuthorizationStatus;
use TigerCore\Auth\IAmCurrentUser;

abstract class ATigerPublicController extends ATigerBaseController {

  protected function onSecurityCheck(IAmCurrentUser $currentUser):RequestAuthorizationStatus{
    return new RequestAuthorizationStatus(RequestAuthorizationStatus::REQUEST_ALLOWED);
  }

}