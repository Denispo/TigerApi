<?php

namespace TigerApi\Auth;

use TigerCore\Auth\ICanGetCurrentUser;
use TigerCore\Auth\IAmCurrentUser;
use TigerCore\ValueObject\VO_BaseId;
use TigerCore\ValueObject\VO_TokenPlainStr;

class TigerJwtUser implements IAmCurrentUser, ICanGetCurrentUser
{

  private TigerAuthTokenClaims $claims;

  /**
   * @param ICanDecodeAuthToken $authTokenDecoder
   * @param VO_TokenPlainStr $tokenPlainStr
   * @throws \TigerCore\Exceptions\InvalidTokenException
   */
  public function __construct(private ICanDecodeAuthToken $authTokenDecoder, private VO_TokenPlainStr $tokenPlainStr)
  {
    $this->claims = $this->authTokenDecoder->decodeAuthToken($this->tokenPlainStr);
  }

  public function isLoggedIn(): bool
  {
    return $this->claims->getUserId()->isValid();
  }

  public function getUserId(): VO_BaseId
  {
    return $this->claims->getUserId();
  }

  public function getCurrentUser(): IAmCurrentUser {
    return $this;
  }
}