<?php

namespace TigerApi;

use TigerCore\Auth\BaseTokenClaims;
use TigerCore\Auth\ICanGetTokenPrivateKey;
use TigerCore\Auth\ICanGetTokenPublicKey;
use TigerCore\ValueObject\VO_BaseId;
use TigerCore\ValueObject\VO_Duration;
use TigerCore\ValueObject\VO_TokenPlainStr;
use TigerCore\ValueObject\VO_TokenPrivateKey;
use TigerCore\ValueObject\VO_TokenPublicKey;

abstract class TigerTokenFactory implements ICanGenerateRefreshTokenForUser , ICanGenerateAuthTokenForUser, ICanGetTokenPublicKey, ICanGetTokenPrivateKey, ICanDecodeRefreshToken, ICanDecodeAuthToken {

  protected abstract function onGetPrivateKey():VO_TokenPrivateKey;
  protected abstract function onGetPublicKey():VO_TokenPublicKey;
  protected abstract function onGetAuthTokenDuration():VO_Duration;
  protected abstract function onGetRefreshTokenDuration():VO_Duration;

  public function generateAuthToken(VO_BaseId $userId): VO_TokenPlainStr {
    return (new TigerAuthToken($this->onGetPrivateKey(), $this->onGetPublicKey()))->getTokenStr($userId);
  }

  public function generateRefreshToken(VO_BaseId $userId): VO_TokenPlainStr {
    return (new TigerRefreshToken($this->onGetPrivateKey(), $this->onGetPublicKey()))->getTokenStr($userId);
  }

  /**
   * @param VO_TokenPlainStr $refreshToken
   * @return BaseTokenClaims
   */
  public function decodeRefreshToken(VO_TokenPlainStr $refreshToken): BaseTokenClaims {
    try {
      return (new TigerRefreshToken($this->onGetPrivateKey(), $this->onGetPublicKey()))->parseToken($refreshToken);
    } catch (\Exception) {
      return new BaseTokenClaims(new VO_BaseId(0), []);
    }

  }

  public function decodeAuthToken(VO_TokenPlainStr $authToken): BaseTokenClaims {
    try {
      return (new TigerAuthToken($this->onGetPrivateKey(), $this->onGetPublicKey()))->parseToken($authToken);
    } catch (\Exception) {
      return new BaseTokenClaims(new VO_BaseId(0), []);
    }
  }

  public function getPrivateKey(): VO_TokenPrivateKey {
    return $this->onGetPrivateKey();
  }

  public function getPublicKey(): VO_TokenPublicKey {
    return $this->onGetPublicKey();
  }

}