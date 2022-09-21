<?php

namespace TigerApi\Auth;

use TigerCore\ValueObject\VO_BaseId;
use TigerCore\ValueObject\VO_TokenPlainStr;

interface  ICanGenerateAuthTokenForUser{

  public function generateAuthToken(VO_BaseId $userId):VO_TokenPlainStr;

}