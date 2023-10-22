<?php

namespace TigerApi\Auth;

use TigerCore\ValueObject\VO_PasswordHash;
use TigerCore\ValueObject\VO_PasswordPlainText;

interface ICanVerifyPasswordAgainstHash {
  public function isPasswordValid(VO_PasswordPlainText $plainPassword, VO_PasswordHash $passwordHash);

}