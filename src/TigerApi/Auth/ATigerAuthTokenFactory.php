<?php

namespace TigerApi\Auth;

use TigerCore\Auth\ICanAddCustomTokenClaim;
use TigerCore\ValueObject\VO_BaseId;
use TigerCore\ValueObject\VO_Duration;
use TigerCore\ValueObject\VO_TokenPlainStr;
use TigerCore\ValueObject\VO_TokenPrivateKey;
use TigerCore\ValueObject\VO_TokenPublicKey;

abstract class ATigerAuthTokenFactory implements IAmAuthTokenFactory {

  protected abstract function onGetPrivateKey():VO_TokenPrivateKey;
  protected abstract function onGetPublicKey():VO_TokenPublicKey;
  protected abstract function onGetAuthTokenDuration():VO_Duration;
  protected abstract function onAddAuthTokenCustomClaims(ICanAddCustomTokenClaim $claimCollector):void;

  public function generateAuthToken(VO_BaseId $userId): VO_TokenPlainStr {
    $claims = new TigerAuthTokenClaims();
    $this->onAddAuthTokenCustomClaims($claims);
    $claims->setUserId($userId);
    return (new TigerAuthToken($this->onGetPrivateKey(), $this->onGetPublicKey()))->generateToken($this->onGetAuthTokenDuration(), $claims);
  }

  public function decodeAuthToken(VO_TokenPlainStr $authToken): TigerAuthTokenClaims {
    try {
      return (new TigerAuthToken($this->onGetPrivateKey(), $this->onGetPublicKey()))->decodeToken($authToken);
    } catch (\Exception) {
      return new TigerAuthTokenClaims();
    }
  }

  public function getPrivateKey(): VO_TokenPrivateKey {
    return $this->onGetPrivateKey();
  }

  public function getPublicKey(): VO_TokenPublicKey {
    return $this->onGetPublicKey();
  }

}