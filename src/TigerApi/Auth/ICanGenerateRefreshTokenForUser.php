<?php

namespace TigerApi\Auth;

use TigerCore\ValueObject\VO_TokenPlainStr;

interface  ICanGenerateRefreshTokenForUser{

  public function generateRefreshToken(string|int $userId):VO_TokenPlainStr;

}