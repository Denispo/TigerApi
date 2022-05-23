<?php

namespace TigerApi;

use Core\Auth\BaseJwtTokenForUser;
use Core\ValueObject\VO_Timestamp;


class TigerRefreshToken extends BaseJwtTokenForUser {

  protected function onGetClaims(): array {
    return [];
  }

  protected function onGetTokenExpirationDate(): VO_Timestamp {
    return (new VO_Timestamp(time()))->addDays(5);
  }

}