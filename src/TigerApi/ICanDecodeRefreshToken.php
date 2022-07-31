<?php

namespace TigerApi;

use TigerCore\Auth\BaseTokenClaims;
use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_TokenPlainStr;

interface ICanDecodeRefreshToken {

  /**
   * @param VO_TokenPlainStr $refreshToken
   * @return BaseTokenClaims
   * @throws InvalidTokenException
   */
  public function decodeRefreshToken(VO_TokenPlainStr $refreshToken):BaseTokenClaims;

}