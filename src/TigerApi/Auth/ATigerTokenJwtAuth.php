<?php

namespace TigerApi\Auth;

use TigerCore\Auth\BaseJwtToken;
use TigerCore\Auth\ICanAddCustomTokenClaim;
use TigerCore\Exceptions\InvalidArgumentException;
use TigerCore\ValueObject\VO_Duration;
use TigerCore\ValueObject\VO_TokenPlainStr;
use TigerCore\ValueObject\VO_TokenPrivateKey;
use TigerCore\ValueObject\VO_TokenPublicKey;

abstract class ATigerTokenJwtAuth implements ICanGenerateAuthTokenForUser, ICanDecodeAuthToken {

  protected abstract function onGetPrivateKey():VO_TokenPrivateKey;
  protected abstract function onGetPublicKey():VO_TokenPublicKey;
  protected abstract function onGetAuthTokenDuration():VO_Duration;
  protected abstract function onAddAuthTokenCustomClaims(ICanAddCustomTokenClaim $claimCollector):void;

  /**
   * @param string|int $userId
   * @return VO_TokenPlainStr
   * @throws InvalidArgumentException
   */
  public function generateAuthToken(string|int $userId): VO_TokenPlainStr {
    $claims = new TigerAuthTokenClaims();
    $this->onAddAuthTokenCustomClaims($claims);
    $claims->setUserId($userId);
    return (new BaseJwtToken())->encodeToken($this->onGetPrivateKey(), $claims, $this->onGetAuthTokenDuration());
  }

  /**
   * @param VO_TokenPlainStr $authToken
   * @return TigerAuthTokenClaims
   */
  public function decodeAuthToken(VO_TokenPlainStr $authToken): TigerAuthTokenClaims {
    try {
      $baseClaims = (new BaseJwtToken())->decodeToken($this->onGetPublicKey(),$authToken);
      return new TigerAuthTokenClaims($baseClaims->getClaims());
    } catch (\Throwable) {
      return new TigerAuthTokenClaims();
    }
  }

}