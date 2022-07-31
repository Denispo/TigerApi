<?php

namespace TigerApi;

use TigerCore\ValueObject\VO_BaseId;
use TigerCore\ValueObject\VO_TokenPlainStr;

interface  ICanGenerateRefreshTokenForUser{

  public function generateRefreshToken(VO_BaseId $userId):VO_TokenPlainStr;

}