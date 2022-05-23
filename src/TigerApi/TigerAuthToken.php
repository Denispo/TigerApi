<?php

namespace TigerApi;

use Core\Auth\BaseJwtTokenForUser;
use Core\ValueObject\VO_Timestamp;


class TigerAuthToken extends BaseJwtTokenForUser {


  protected function onGetClaims(): array {
    return [];
  }

  protected function onGetTokenExpirationDate(): VO_Timestamp {
    return (new VO_Timestamp(time()))->addMinutes(60);
  }

}