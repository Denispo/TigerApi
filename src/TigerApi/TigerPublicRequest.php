<?php

namespace TigerApi;

use TigerCore\Auth\ICurrentUser;

abstract class TigerPublicRequest extends TigerRequest {

  protected function onSecurityCheck(ICurrentUser $currentUser):RequestSecurityStatus{
    return new RequestSecurityStatus(RequestSecurityStatus::REQUEST_ALLOWED);
  }

}