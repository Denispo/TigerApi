<?php

namespace TigerApi\Controller;

use TigerApi\Request\RequestAuthorizationStatus;
use TigerCore\Auth\IAmCurrentUser;

abstract class ATigerPublicController extends ATigerBaseController {

  protected function onGetAuthorizationStatus(IAmCurrentUser $currentUser):RequestAuthorizationStatus{
    return new RequestAuthorizationStatus(RequestAuthorizationStatus::REQUEST_ALLOWED);
  }

}