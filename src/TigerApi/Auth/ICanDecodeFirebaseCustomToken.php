<?php

namespace TigerApi\Auth;

use TigerCore\Auth\BaseTokenClaims;
use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_TokenPlainStr;

interface ICanDecodeFirebaseCustomToken {

  /**
   * @param VO_TokenPlainStr $refreshToken
   * @return BaseTokenClaims
   * @throws InvalidTokenException
   */
  public function decodeToken(VO_TokenPlainStr $refreshToken):BaseTokenClaims;

}