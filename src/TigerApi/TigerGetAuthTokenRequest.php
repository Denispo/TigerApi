<?php

namespace TigerApi;

use TigerCore\Auth\ICanGenerateAuthTokenForUser;
use TigerCore\Auth\ICanDecodeRefreshToken;
use TigerCore\Auth\ICurrentUser;
use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\Payload\AuthTokenPayload;
use TigerCore\Request\BaseRequest;
use TigerCore\Request\IOnAddToPayload;
use TigerCore\Request\ICanMatch;
use TigerCore\Request\RequestData;
use TigerCore\Response\BaseResponseException;
use TigerCore\Response\ICanAddToPayload;
use TigerCore\ValueObject\VO_TokenPlainStr;

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