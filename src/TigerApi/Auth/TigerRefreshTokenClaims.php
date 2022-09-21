<?php

namespace TigerApi\Auth;

use TigerCore\Auth\BaseTokenClaims;
use TigerCore\ValueObject\VO_BaseId;


class TigerRefreshTokenClaims extends BaseTokenClaims {

  public function getUserId():VO_BaseId {
    return new VO_BaseId($this->getClaims()['uid'] ?? 0);
  }

  public function setUserId(VO_BaseId $userId):void {
    $this->claims['uid'] = $userId->getValue();
  }


}