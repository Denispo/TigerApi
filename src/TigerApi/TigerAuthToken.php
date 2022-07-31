<?php

namespace TigerApi;

use TigerCore\Auth\BaseJwtToken;
use TigerCore\Auth\ICanGetTokenPrivateKey;
use TigerCore\Auth\ICanGetTokenPublicKey;
use TigerCore\Auth\JwtTokenSettings;
use TigerCore\ValueObject\VO_Duration;
use TigerCore\ValueObject\VO_TokenPrivateKey;
use TigerCore\ValueObject\VO_TokenPublicKey;


class TigerAuthToken extends BaseJwtToken implements ICanGetTokenPublicKey , ICanGetTokenPrivateKey {

  private JwtTokenSettings $tokenSettings;

  public function __construct(
    private VO_TokenPrivateKey $privateKey,
    private VO_TokenPublicKey $publicKey,
  ) {
    $this->tokenSettings = new JwtTokenSettings($this, $this);
  }

  public function setTokenDuration(VO_Duration $duration): void {
    $this->tokenSettings->setDuration($duration);
  }

  public function generateToken() {
    $this->doEncodeToken()

  }

  protected function onGetTokenSettings(): JwtTokenSettings {
    return $this->tokenSettings;
  }

  public function getPrivateKey(): VO_TokenPrivateKey {
    return $this->privateKey;
  }

  public function getPublicKey(): VO_TokenPublicKey {
    return $this->publicKey;
  }

  protected function onGetClaims(): array {
    return [];
  }

}