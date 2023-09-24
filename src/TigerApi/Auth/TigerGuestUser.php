<?php

namespace TigerApi\Auth;

class TigerGuestUser implements IAmCurrentUser, ICanGetCurrentUser
{

  public function isAuthenticated(): bool
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