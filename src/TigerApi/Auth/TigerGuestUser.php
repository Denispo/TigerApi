<?php

namespace TigerApi\Auth;

use TigerCore\Auth\ICanGetCurrentUser;
use TigerCore\Auth\IAmCurrentUser;

class TigerGuestUser implements IAmCurrentUser, ICanGetCurrentUser
{

  public function isLoggedIn(): bool
  {
    return false;
  }

  public function getUserId(): int
  {
    return 0;
  }

  public function getCurrentUser(): IAmCurrentUser {
    return $this;
  }
}