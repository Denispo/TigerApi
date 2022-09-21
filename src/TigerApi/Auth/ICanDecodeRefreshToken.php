<?php

namespace TigerApi\Auth;

use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_TokenPlainStr;

interface ICanDecodeRefreshToken {

  /**
   * @param VO_TokenPlainStr $refreshToken
   * @return TigerRefreshTokenClaims
   * @throws InvalidTokenException
   */
  public function decodeRefreshToken(VO_TokenPlainStr $refreshToken):TigerRefreshTokenClaims;

}