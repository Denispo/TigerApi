<?php

namespace TigerApi\Auth;

use TigerCore\ValueObject\VO_PasswordHash;
use TigerCore\ValueObject\VO_PasswordPlainText;

interface ICanGetPasswordHash {
  public function getHashForPassword(VO_PasswordPlainText $plainPassword):VO_PasswordHash;

}