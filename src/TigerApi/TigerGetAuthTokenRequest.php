<?php

namespace TigerApi;

use Nette\Http\IRequest;
use TigerCore\Auth\BaseDecodedTokenData;
use TigerCore\Auth\ICanGenerateAuthTokenForUser;
use TigerCore\Auth\ICanDecodeRefreshToken;
use TigerCore\Auth\ICurrentUser;
use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\Payload\AuthTokenPayload;
use TigerCore\Request\BaseRequest;
use TigerCore\Request\RequestParam;
use TigerCore\Response\BaseResponseException;
use TigerCore\Response\ICanAddToPayload;
use TigerCore\ValueObject\VO_BaseId;
use TigerCore\ValueObject\VO_TokenPlainStr;

abstract class TigerGetAuthTokenRequest extends BaseRequest {

  #[RequestParam('refreshtoken')]
  public VO_TokenPlainStr $refreshToken;

  //---------------------------------------------

  private VO_TokenPlainStr|null $authToken = null;

  /**
   * @param VO_TokenPlainStr $refreshToken
   * @return BaseDecodedTokenData
   * @throws  InvalidTokenException
   */
  abstract protected function onGetDecodedRefreshToken(VO_TokenPlainStr $refreshToken):BaseDecodedTokenData;

  abstract protected function onGenerateNewAuthTokenForUser(VO_BaseId $userId):VO_TokenPlainStr;

  public function onMatch(ICurrentUser $currentUser, ICanAddToPayload $payload, IRequest $httpRequest): void {
    try {
      $parsedRefreshToken = $this->onGetDecodedRefreshToken($this->refreshToken);
    } catch (InvalidTokenException $e) {
      throw new BaseResponseException($e->getMessage());
    }

    if (!$parsedRefreshToken->getUserId()->isValid()) {
      throw new BaseResponseException('invalid user id in token');
    }

    $authToken = $this->onGenerateNewAuthTokenForUser($parsedRefreshToken->getUserId());

    if (!$authToken->isEmpty()) {
      $payload->addToPayload(new AuthTokenPayload($this->authToken));
    }  else {
      throw new BaseResponseException('Can not generate Auth token');
    }
  }

}