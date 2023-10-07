<?php

namespace TigerApi\Auth;

use TigerCore\ValueObject\VO_TokenPlainStr;

interface  ICanGenerateFirebaseCustomToken{

  public function generateToken(string|int $userId):VO_TokenPlainStr;

}