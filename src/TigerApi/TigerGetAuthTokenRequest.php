<?php

namespace TigerApi;

use Core\Auth\ICanGenerateAuthTokenForUser;
use Core\Auth\ICanDecodeRefreshToken;
use Core\Auth\ICurrentUser;
use Core\Exceptions\InvalidTokenException;
use Core\Payload\AuthTokenPayload;
use Core\Request\BaseRequest;
use Core\Request\IOnAddToPayload;
use Core\Request\ICanMatch;
use Core\Request\RequestData;
use Core\Response\BaseResponseException;
use Core\Response\ICanAddToPayload;
use Core\ValueObject\VO_TokenPlainStr;

abstract class TigerGetAuthTokenRequest extends BaseRequest implements ICanMatch, IOnAddToPayload {

  #[RequestData('refreshtoken')]
  public string $refreshToken;

  //---------------------------------------------

  private VO_TokenPlainStr|null $authToken = null;

  public function __construct(
    private ICanDecodeRefreshToken $refreshTokenDecoder,
    private ICanGenerateAuthTokenForUser $authTokenGenerator) {
  }

  public function onMatch(ICurrentUser $currentUser): void {
    try {
      $parsedRefreshToken = $this->refreshTokenDecoder->decodeRefreshToken(new VO_TokenPlainStr($this->refreshToken));
    } catch (InvalidTokenException $e) {
      throw new BaseResponseException($e->getMessage());
    }

    if (!$parsedRefreshToken->getUserId()->isValid()) {
      throw new BaseResponseException('invalid user id in token');
    }

    $this->authToken = $this->authTokenGenerator->generateAuthToken($parsedRefreshToken->getUserId());
  }

  public function onAddPayload(ICanAddToPayload $payload): void {
    if ($this->authToken && !$this->authToken->isEmpty()) {
      $payload->addToPayload(new AuthTokenPayload($this->authToken));
    }
  }
}