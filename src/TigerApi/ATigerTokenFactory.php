<?php

namespace TigerApi;

use TigerCore\Auth\ICanAddCustomTokenClaim;
use TigerCore\Auth\ICanGetTokenPrivateKey;
use TigerCore\Auth\ICanGetTokenPublicKey;
use TigerCore\ValueObject\VO_BaseId;
use TigerCore\ValueObject\VO_Duration;
use TigerCore\ValueObject\VO_TokenPlainStr;
use TigerCore\ValueObject\VO_TokenPrivateKey;
use TigerCore\ValueObject\VO_TokenPublicKey;

abstract class ATigerTokenFactory implements ICanGenerateRefreshTokenForUser , ICanGenerateAuthTokenForUser, ICanGetTokenPublicKey, ICanGetTokenPrivateKey, ICanDecodeRefreshToken, ICanDecodeAuthToken {

  protected abstract function onGetPrivateKey():VO_TokenPrivateKey;
  protected abstract function onGetPublicKey():VO_TokenPublicKey;
  protected abstract function onGetAuthTokenDuration():VO_Duration;
  protected abstract function onGetRefreshTokenDuration():VO_Duration;
  protected abstract function onAddAuthTokenCustomClaims(ICanAddCustomTokenClaim $claimCollector):void;
  protected abstract function onAddRefreshTokenCustomClaims(ICanAddCustomTokenClaim $claimCollector):void;

  public function generateAuthToken(VO_BaseId $userId): VO_TokenPlainStr {
    $claims = new TigerAuthTokenClaims();
    $this->onAddAuthTokenCustomClaims($claims);
    $claims->setUserId($userId);
    return (new TigerAuthToken($this->onGetPrivateKey(), $this->onGetPublicKey()))->generateToken($this->onGetAuthTokenDuration(), $claims);
  }

  public function generateRefreshToken(VO_BaseId $userId): VO_TokenPlainStr {
    $claims = new TigerAuthTokenClaims();
    $this->onAddRefreshTokenCustomClaims($claims);
    $claims->setUserId($userId);
    return (new TigerRefreshToken($this->onGetPrivateKey(), $this->onGetPublicKey()))->generateToken($this->onGetRefreshTokenDuration(), $claims);
  }

  /**
   * @param VO_TokenPlainStr $refreshToken
   * @return TigerAuthTokenClaims
   */
  public function decodeRefreshToken(VO_TokenPlainStr $refreshToken): TigerAuthTokenClaims {
    try {
      return (new TigerRefreshToken($this->onGetPrivateKey(), $this->onGetPublicKey()))->decodeToken($refreshToken);
    } catch (\Exception) {
      return new TigerAuthTokenClaims();
    }

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