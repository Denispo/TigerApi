<?php

namespace TigerApi;

use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_TokenPlainStr;

interface ICanDecodeAuthToken {

  /**
   * @param VO_TokenPlainStr $authToken
   * @return TigerAuthTokenClaims
   * @throws InvalidTokenException
   */
  public function decodeAuthToken(VO_TokenPlainStr $authToken):TigerAuthTokenClaims;

}