<?php

namespace TigerApi\Auth;

use TigerCore\Auth\BaseJwtToken;
use TigerCore\Auth\BaseTokenClaims;
use TigerCore\Auth\FirebaseCustomToken;
use TigerCore\Auth\ICanAddCustomTokenClaim;
use TigerCore\Exceptions\InvalidArgumentException;
use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_Duration;
use TigerCore\ValueObject\VO_FullPathFileName;
use TigerCore\ValueObject\VO_TokenPlainStr;
use TigerCore\ValueObject\VO_TokenPrivateKey;
use TigerCore\ValueObject\VO_TokenPublicKey;

abstract class ATigerTokenJwtFirebaseCustomToken implements ICanGenerateFirebaseCustomToken , ICanDecodeFirebaseCustomToken {

  protected abstract function onGetPublicKey():VO_TokenPublicKey;
  protected abstract function onGetFirebaseServiceAccountJson():VO_FullPathFileName|array;
  protected abstract function onAddTokenCustomClaims(ICanAddCustomTokenClaim $claimCollector):void;


  /**
   * @param string|int $userId
   * @return VO_TokenPlainStr
   * @throws InvalidArgumentException
   * @throws InvalidTokenException
   */
  public function generateToken(string|int $userId): VO_TokenPlainStr {
    $claims = new BaseTokenClaims();
    $this->onAddTokenCustomClaims($claims);
    return FirebaseCustomToken::generateToken($this->onGetFirebaseServiceAccountJson(), $userId, $claims);
  }

  /**
   * @param VO_TokenPlainStr $refreshToken
   * @return BaseTokenClaims
   */
  public function decodeToken(VO_TokenPlainStr $refreshToken): BaseTokenClaims {
    try {
      $decodedToken =  FirebaseCustomToken::decodeToken($this->onGetPublicKey(),$refreshToken);
    } catch (\Exception) {
      return new BaseTokenClaims();
    }

  }

}