<?php

namespace TigerApi;

use TigerCore\Auth\ICanGetCurrentUser;
use TigerCore\Auth\IAmCurrentUser;
use TigerCore\ValueObject\VO_BaseId;

class TigerJwtUser implements IAmCurrentUser, ICanGetCurrentUser
{

  public function __construct(private TigerAuthTokenClaims $tokenData)
  {

  }

  public function isLoggedIn(): bool
  {
    return $this->getUserId()->isValid();
  }

  public function getUserId(): VO_BaseId
  {
    return $this->tokenData->getUserId();
  }

  public function getCurrentUser(): IAmCurrentUser {
    return $this;
  }
}