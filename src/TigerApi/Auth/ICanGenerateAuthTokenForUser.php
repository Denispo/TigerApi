<?php

namespace TigerApi\Auth;

use TigerCore\ValueObject\VO_TokenPlainStr;

interface  ICanGenerateAuthTokenForUser{

  public function generateAuthToken(string|int $userId):VO_TokenPlainStr;

}