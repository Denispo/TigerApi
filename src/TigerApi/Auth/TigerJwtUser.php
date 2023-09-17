<?php

namespace TigerApi\Auth;

use TigerCore\Auth\ICanGetCurrentUser;
use TigerCore\Auth\IAmCurrentUser;
use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_TokenPlainStr;

class TigerJwtUser implements IAmCurrentUser, ICanGetCurrentUser
{

  private TigerAuthTokenClaims $claims;

  /**
   * @param ICanDecodeAuthToken $authTokenDecoder
   * @param VO_TokenPlainStr $tokenPlainStr
   * @throws InvalidTokenException
   */
  public function __construct(private ICanDecodeAuthToken $authTokenDecoder, private VO_TokenPlainStr $tokenPlainStr)
  {
    $this->claims = $this->authTokenDecoder->decodeAuthToken($this->tokenPlainStr);
  }

  public function isLoggedIn(): bool
  {
    $userId = $this->claims->getUserId();
    return (is_int($userId) && $userId !== 0) || (is_string($userId) && trim($userId) !== '');
  }

  public function getUserId(): string|int
  {
    return $this->claims->getUserId();
  }

  public function getCurrentUser(): IAmCurrentUser {
    return $this;
  }
}