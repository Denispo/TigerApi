<?php

namespace TigerApi\Request;

use Nette\Http\IRequest;
use TigerApi\Auth\TigerAuthTokenClaims;
use TigerCore\Exceptions\InvalidTokenException;
use TigerApi\Payload\AuthTokenPayload;
use TigerCore\Request\RequestParam;
use TigerCore\Requests\RP_String;
use TigerCore\Response\BaseResponseException;
use TigerCore\Response\ICanAddPayload;
use TigerCore\ValueObject\VO_BaseId;
use TigerCore\ValueObject\VO_TokenPlainStr;

abstract class ATigerGenerateAuthTokenFromRefreshTokenRequest extends ATigerPublicRequest {

  #[RequestParam('refreshtoken')]
  public RP_String $refreshToken;

  //---------------------------------------------

  private VO_TokenPlainStr|null $authToken = null;

  /**
   * @param VO_TokenPlainStr $refreshToken
   * @return TigerAuthTokenClaims
   * @throws  InvalidTokenException
   */
  abstract protected function onGetDecodedRefreshToken(VO_TokenPlainStr $refreshToken):TigerAuthTokenClaims;

  abstract protected function onGenerateNewAuthTokenForUser(VO_BaseId $userId):VO_TokenPlainStr;

   protected function onValidateParams(ICanSetRequestParamIsInvalid $validator) {
     if ($this->refreshToken->isEmpty()) {
       $validator->setRequestParamIsInvalid($this->refreshToken, 'Token is empty');
     }
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

     if (!$parsedRefreshToken->getUserId()->isValid()) {
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