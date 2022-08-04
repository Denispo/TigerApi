<?php

namespace TigerApi;

use TigerCore\Auth\BaseJwtToken;
use TigerCore\Auth\ICanGetTokenPrivateKey;
use TigerCore\Auth\ICanGetTokenPublicKey;
use TigerCore\Auth\JwtTokenSettings;
use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_Duration;
use TigerCore\ValueObject\VO_TokenPlainStr;
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

  public function generateToken(VO_Duration $duration, TigerAuthTokenClaims $claims):VO_TokenPlainStr {
    return $this->doEncodeToken($claims, $duration);
  }

  /**
   * @param VO_TokenPlainStr $tokenStr
   * @return TigerAuthTokenClaims
   * @throws InvalidTokenException
   */
  public function decodeToken(VO_TokenPlainStr $tokenStr):TigerAuthTokenClaims {
    $data = $this->doDecodeToken($tokenStr);
    return new TigerAuthTokenClaims($data->getClaims());
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

}