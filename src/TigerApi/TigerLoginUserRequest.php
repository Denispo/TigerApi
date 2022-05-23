<?php

namespace TigerApi;

use TigerCore\Constants\PasswordValidity;
use TigerCore\Payload\AuthTokenPayload;
use TigerCore\Payload\RefreshTokenPayload;
use TigerCore\Request\BaseLoginUserRequest;
use TigerCore\Response\ICanAddToPayload;
use TigerCore\Response\UnauthorizedException;
use TigerCore\ValueObject\VO_BaseId;
use TigerCore\ValueObject\VO_Password;
use TigerCore\ValueObject\VO_TokenPlainStr;


abstract class TigerLoginUserRequest extends BaseLoginUserRequest {

  private VO_TokenPlainStr $authToken;
  private VO_TokenPlainStr $refreshToken;

  protected abstract function onGetPasswordHash(VO_BaseId $userId): string;
  protected abstract function onGetAuthToken(VO_BaseId $userId):VO_TokenPlainStr;
  protected abstract function onGetRefreshToken(VO_BaseId $userId):VO_TokenPlainStr;


  protected function onVerifyPassword(VO_Password $password, VO_BaseId $userId): PasswordValidity {
    $passwordHash = $this->onGetPasswordHash($userId);
    $isValid = password_verify($password->getValue(), $passwordHash);
    return PasswordValidity::createFromBoolean($isValid);
  }

  protected function onLoginComplete(VO_BaseId $userId):void {
    $this->authToken = $this->onGetAuthToken($userId);
    $this->refreshToken = $this->onGetRefreshToken($userId);
  }

  public function onAddPayload(ICanAddToPayload $payload): void {
    if ($this->authToken->isEmpty() || $this->refreshToken->isEmpty()) {
      throw new UnauthorizedException('Can not generate auth or refresh tokens');
    } else {
      $payload->addToPayload(new AuthTokenPayload($this->authToken));
      $payload->addToPayload(new RefreshTokenPayload($this->refreshToken));
    }

  }
}