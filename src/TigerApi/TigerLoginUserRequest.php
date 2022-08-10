<?php

namespace TigerApi;

use Nette\Http\IRequest;
use TigerCore\Constants\PasswordValidity;
use TigerCore\Payload\RefreshTokenPayload;
use TigerCore\Request\RequestParam;
use TigerCore\Requests\RP_String;
use TigerCore\Response\ICanAddPayload;
use TigerCore\Response\UnauthorizedException;
use TigerCore\ValueObject\VO_BaseId;
use TigerCore\ValueObject\VO_Email;
use TigerCore\ValueObject\VO_Password;
use TigerCore\ValueObject\VO_TokenPlainStr;


 abstract class TigerLoginUserRequest extends TigerPublicRequest {

  #[RequestParam('name')]
  public RP_String $userName;

  #[RequestParam('email')]
  public RP_String $userEmail;

  #[RequestParam('password')]
  public RP_String $userPassword;


  protected abstract function onGetPasswordHash(VO_BaseId $userId): string;
  protected abstract function onGetRefreshToken(VO_BaseId $userId):VO_TokenPlainStr;
  protected abstract function onGetUserIdByCredentials(string $loginName = '', VO_Email|null $loginEmail = null):VO_BaseId;

  protected function onVerifyPassword(VO_Password $password, VO_BaseId $userId): PasswordValidity {
    $passwordHash = $this->onGetPasswordHash($userId);
    $isValid = password_verify($password->getValue(), $passwordHash);
    return PasswordValidity::createFromBoolean($isValid);
  }

  protected function onLoginComplete(VO_BaseId $userId, ICanAddPayload $payload):void {
    $refreshToken = $this->onGetRefreshToken($userId);

    if ($refreshToken->isEmpty()) {
      throw new UnauthorizedException('Can not generate refresh token');
    } else {
      $payload->addPayload(new RefreshTokenPayload($refreshToken));
    }

  }

   protected function onValidateParams(ICanSetRequestParamIsInvalid $validator) {
   }

   protected function onProcessRequest(ICanAddPayload $payload, IRequest $httpRequest): void {

     $userEmail = new VO_Email($this->userName->getValueAsString());
     if (!$userEmail->isValid()) {
       $userEmail = new VO_Email($this->userEmail->getValueAsString());
     }
     if (!$userEmail->isValid()) {
       $userEmail = null;
     }

     $userId = $this->onGetUserIdByCredentials($this->userName->getValueAsString(), $userEmail);

     if (!$userId->isValid()) {
       throw new UnauthorizedException();
     }

     $passwordValidity = $this->onVerifyPassword(new VO_Password($this->userPassword->getValueAsString()), $userId);

     if ($passwordValidity->IsSetTo(PasswordValidity::PWD_INVALID)) {
       throw new UnauthorizedException();
     }

     $this->onLoginComplete($userId, $payload);

   }
 }