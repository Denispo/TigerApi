<?php

namespace TigerApi;

use TigerCore\Auth\BaseTokenClaims;
use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_TokenPlainStr;

interface ICanDecodeAuthToken {

  /**
   * @param VO_TokenPlainStr $authToken
   * @return BaseTokenClaims
   * @throws InvalidTokenException
   */
  public function decodeAuthToken(VO_TokenPlainStr $authToken):BaseTokenClaims;

}