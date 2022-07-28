<?php

namespace TigerApi;

use TigerCore\Constants\PasswordValidity;
use TigerCore\Payload\RefreshTokenPayload;
use TigerCore\Request\BaseLoginUserRequest;
use TigerCore\Response\ICanAddToPayload;
use TigerCore\Response\UnauthorizedException;
use TigerCore\ValueObject\VO_BaseId;
use TigerCore\ValueObject\VO_Password;
use TigerCore\ValueObject\VO_TokenPlainStr;


abstract class TigerLoginUserRequest extends BaseLoginUserRequest {

  protected abstract function onGetPasswordHash(VO_BaseId $userId): string;
  protected abstract function onGetRefreshToken(VO_BaseId $userId):VO_TokenPlainStr;


  protected function onVerifyPassword(VO_Password $password, VO_BaseId $userId): PasswordValidity {
    $passwordHash = $this->onGetPasswordHash($userId);
    $isValid = password_verify($password->getValue(), $passwordHash);
    return PasswordValidity::createFromBoolean($isValid);
  }

  protected function onLoginComplete(VO_BaseId $userId, ICanAddToPayload $payload):void {
    $refreshToken = $this->onGetRefreshToken($userId);

    if ($refreshToken->isEmpty()) {
      throw new UnauthorizedException('Can not generate refresh token');
    } else {
      $payload->addToPayload(new RefreshTokenPayload($refreshToken));
    }

  }

}