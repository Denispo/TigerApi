<?php

namespace TigerApi\Request;

use Nette\Http\IRequest;
use TigerApi\Auth\TigerRefreshTokenClaims;
use TigerCore\Exceptions\InvalidTokenException;
use TigerApi\Payload\AuthTokenPayload;
use TigerCore\Request\RequestParam;
use TigerCore\Request\Validator\RPCheck_IsNotEmptyString;
use TigerCore\Requests\RP_String;
use TigerCore\Response\BaseResponseException;
use TigerCore\Response\ICanAddPayload;
use TigerCore\ValueObject\VO_BaseId;
use TigerCore\ValueObject\VO_TokenPlainStr;

abstract class ATigerGenerateAuthTokenFromRefreshTokenRequest extends ATigerPublicRequest {

  #[RequestParam('refreshtoken')]
  #[RPCheck_IsNotEmptyString]
  public RP_String $refreshToken;

  //---------------------------------------------

  private VO_TokenPlainStr|null $authToken = null;

  /**
   * @param VO_TokenPlainStr $refreshToken
   * @return TigerRefreshTokenClaims
   * @throws  InvalidTokenException
   */
  abstract protected function onGetDecodedRefreshToken(VO_TokenPlainStr $refreshToken):TigerRefreshTokenClaims;

  abstract protected function onGenerateNewAuthTokenForUser(VO_BaseId $userId):VO_TokenPlainStr;

   protected function onValidateParams(ICanSetRequestParamIsInvalid $validator) {
   }

  /**
   * @param ICanAddPayload $payload
   * @param IRequest $httpRequest
   * @return void
   * @throws BaseResponseException
   * @throws \ReflectionException
   */
   protected function onProcessRequest(ICanAddPayload $payload, IRequest $httpRequest): void {
     try {
       $parsedRefreshToken = $this->onGetDecodedRefreshToken(new VO_TokenPlainStr($this->refreshToken->getValueAsString()));
     } catch (InvalidTokenException $e) {
       throw new BaseResponseException($e->getMessage());
     }

     if ($parsedRefreshToken->getUserId()->isEmpty()) {
       throw new BaseResponseException('invalid user id in token');
     }

     $authToken = $this->onGenerateNewAuthTokenForUser($parsedRefreshToken->getUserId());

     if (!$authToken->isEmpty()) {
       $payload->addPayload(new AuthTokenPayload($this->authToken));
     }  else {
       throw new BaseResponseException('Can not generate Auth token');
     }
   }
 }