<?php

namespace TigerApi;

use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_TokenPlainStr;

interface ICanDecodeRefreshToken {

  /**
   * @param VO_TokenPlainStr $refreshToken
   * @return TigerAuthTokenClaims
   * @throws InvalidTokenException
   */
  public function decodeRefreshToken(VO_TokenPlainStr $refreshToken):TigerAuthTokenClaims;

}