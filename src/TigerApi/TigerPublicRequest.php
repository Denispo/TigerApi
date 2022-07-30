<?php

namespace TigerApi;

use TigerCore\Auth\ICurrentUser;

abstract class TigerPublicRequest extends TigerRequest {

  protected function onSecurityCheck(ICurrentUser $currentUser):RequestSecurityCheck{
    return new RequestSecurityCheck(RequestSecurityCheck::REQUEST_ALLOWED);
  }

}