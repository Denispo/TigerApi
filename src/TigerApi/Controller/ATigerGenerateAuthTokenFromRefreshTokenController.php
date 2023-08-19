<?php

namespace TigerApi\Controller;

use Nette\Http\IRequest;
use TigerApi\Auth\TigerRefreshTokenClaims;
use TigerApi\Payload\AuthTokenPayload;
use TigerApi\Request\ICanSetRequestParamIsInvalid;
use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\Payload\IAmPayloadContainer;
use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\Request\RequestParam;
use TigerCore\Request\RP_String;
use TigerCore\Request\Validator\AssertNoEmptyString;
use TigerCore\Response\BaseResponseException;
use TigerCore\Response\S500_InternalServerErrorException;
use TigerCore\ValueObject\VO_TokenPlainStr;

abstract class ATigerGenerateAuthTokenFromRefreshTokenController extends ATigerController {

  #[RequestParam('refreshtoken')]
  #[AssertNoEmptyString]
  public RP_String $refreshToken;

  //---------------------------------------------

  private VO_TokenPlainStr|null $authToken = null;

  /**
   * @param VO_TokenPlainStr $refreshToken
   * @return TigerRefreshTokenClaims
   * @throws  InvalidTokenException
   */
  abstract protected function onGetDecodedRefreshToken(VO_TokenPlainStr $refreshToken):TigerRefreshTokenClaims;

  abstract protected function onGenerateNewAuthTokenForUser(string|int $userId):VO_TokenPlainStr;

  abstract function onGetPayloadContainer():IAmPayloadContainer;

   protected function onValidateParams(ICanSetRequestParamIsInvalid $validator):void {
   }

  /**
   * @param IRequest $httpRequest
   * @return ICanGetPayloadRawData
   * @throws BaseResponseException
   * @throws S500_InternalServerErrorException
   */
  protected function onProcessRequest(IRequest $httpRequest):ICanGetPayloadRawData {
    try {
      $parsedRefreshToken = $this->onGetDecodedRefreshToken(new VO_TokenPlainStr($this->refreshToken->getValueAsString()));
    } catch (InvalidTokenException $e) {
      throw new BaseResponseException($e->getMessage());
    }

     $userId = $parsedRefreshToken->getUserId();
     if (!((is_int($userId) && $userId >= 0) || (is_string($userId) && (trim($userId) !== '')))) {
      throw new BaseResponseException('invalid user id in token');
    }

    $authToken = $this->onGenerateNewAuthTokenForUser($parsedRefreshToken->getUserId());

    $payload = $this->onGetPayloadContainer();

    if (!$authToken->isEmpty()) {
      $payload->addPayload(new AuthTokenPayload($this->authToken));
    }  else {
      throw new BaseResponseException('Can not generate Auth token');
    }
    return $payload;
   }
 }