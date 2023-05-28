<?php

namespace TigerApi\Auth;

use TigerCore\Auth\BaseTokenClaims;

class TigerAuthTokenClaims extends BaseTokenClaims {

  public function getUserId():string|int {
    return $this->getClaims()['uid'] ?? 0;
  }

  public function setUserId(string|int $userId):void {
    $this->claims['uid'] = $userId;
  }


}