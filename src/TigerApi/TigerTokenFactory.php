<?php

namespace TigerApi;

use Core\Auth\BaseDecodedTokenData;
use Core\Auth\ICanDecodeAuthToken;
use Core\Auth\ICanGenerateAuthTokenForUser;
use Core\Auth\ICanGenerateRefreshTokenForUser;
use Core\Auth\ICanGetTokenPrivateKey;
use Core\Auth\ICanGetTokenPublicKey;
use Core\Auth\ICanDecodeRefreshToken;
use Core\Exceptions\InvalidTokenException;
use Core\ValueObject\VO_BaseId;
use Core\ValueObject\VO_Duration;
use Core\ValueObject\VO_TokenPlainStr;
use Core\ValueObject\VO_TokenPrivateKey;
use Core\ValueObject\VO_TokenPublicKey;

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
   * @return BaseDecodedTokenData
   * @throws InvalidTokenException
   */
  public function decodeRefreshToken(VO_TokenPlainStr $refreshToken): BaseDecodedTokenData {
    return (new TigerRefreshToken($this->onGetPrivateKey(), $this->onGetPublicKey()))->parseToken($refreshToken);
  }

  public function decodeAuthToken(VO_TokenPlainStr $authToken): BaseDecodedTokenData {
    return (new TigerAuthToken($this->onGetPrivateKey(), $this->onGetPublicKey()))->parseToken($authToken);
  }

  public function getPrivateKey(): VO_TokenPrivateKey {
    return $this->onGetPrivateKey();
  }

  public function getPublicKey(): VO_TokenPublicKey {
    return $this->onGetPublicKey();
  }

}