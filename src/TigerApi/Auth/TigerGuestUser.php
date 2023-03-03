<?php

namespace TigerApi\Auth;

use TigerCore\Auth\ICanGetCurrentUser;
use TigerCore\Auth\IAmCurrentUser;
use TigerCore\ValueObject\VO_BaseId;

class TigerGuestUser implements IAmCurrentUser, ICanGetCurrentUser
{

  private VO_BaseId $userId;

  public function __construct()
  {
    $this->userId = new VO_BaseId(0);
  }

  public function isLoggedIn(): bool
  {
    return false;
  }

  public function getUserId(): VO_BaseId
  {
    return $this->userId;
  }

  public function getCurrentUser(): IAmCurrentUser {
    return $this;
  }
}