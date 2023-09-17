<?php

namespace TigerApi\Auth;

use TigerCore\Auth\BaseJwtToken;
use TigerCore\Auth\ICanAddCustomTokenClaim;
use TigerCore\ValueObject\VO_Duration;
use TigerCore\ValueObject\VO_TokenPlainStr;
use TigerCore\ValueObject\VO_TokenPrivateKey;
use TigerCore\ValueObject\VO_TokenPublicKey;

abstract class ATigerRefreshToken implements ICanGenerateRefreshTokenForUser , ICanDecodeRefreshToken {

  protected abstract function onGetPrivateKey():VO_TokenPrivateKey;
  protected abstract function onGetPublicKey():VO_TokenPublicKey;
  protected abstract function onGetRefreshTokenDuration():VO_Duration;
  protected abstract function onAddRefreshTokenCustomClaims(ICanAddCustomTokenClaim $claimCollector):void;


  /**
   * @param string|int $userId
   * @return VO_TokenPlainStr
   * @throws \TigerCore\Exceptions\InvalidArgumentException
   */
  public function generateRefreshToken(string|int $userId): VO_TokenPlainStr {
    $claims = new TigerRefreshTokenClaims();
    $this->onAddRefreshTokenCustomClaims($claims);
    $claims->setUserId($userId);
    return (new BaseJwtToken())->encodeToken($this->onGetPrivateKey(), $claims, $this->onGetRefreshTokenDuration());
  }

  /**
   * @param VO_TokenPlainStr $refreshToken
   * @return TigerRefreshTokenClaims
   */
  public function decodeRefreshToken(VO_TokenPlainStr $refreshToken): TigerRefreshTokenClaims {
    try {
      $baseClaims = (new BaseJwtToken())->decodeToken($this->onGetPublicKey(),$refreshToken);
      return new TigerRefreshTokenClaims($baseClaims->getClaims());
    } catch (\Exception) {
      return new TigerRefreshTokenClaims();
    }

  }

}