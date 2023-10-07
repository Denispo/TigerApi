<?php

namespace TigerApi\Auth;

use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_TokenPlainStr;

interface ICanDecodeFirebaseCustomToken {

  /**
   * @param VO_TokenPlainStr $refreshToken
   * @return TigerRefreshTokenClaims
   * @throws InvalidTokenException
   */
  public function decodeToken(VO_TokenPlainStr $refreshToken):TigerRefreshTokenClaims;

}