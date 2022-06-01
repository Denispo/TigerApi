<?php

namespace TigerApi;

use TigerCore\Auth\BaseDecodedTokenData;
use TigerCore\Auth\ICurrentUser;
use TigerCore\ValueObject\VO_BaseId;

class TigerJwtUser implements ICurrentUser
{

  public function __construct(private BaseDecodedTokenData $tokenData)
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
}