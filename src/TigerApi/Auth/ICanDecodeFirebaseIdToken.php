<?php

namespace TigerApi\Auth;

use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_TokenPlainStr;

interface ICanDecodeFirebaseIdToken {

  /**
   * @param VO_TokenPlainStr $token
   * @return TigerAuthTokenClaims
   * @throws InvalidTokenException
   */
  public function decodeFirebaseIdToken(VO_TokenPlainStr $token):TigerAuthTokenClaims;

}