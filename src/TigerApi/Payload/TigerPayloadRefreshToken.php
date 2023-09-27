<?php

namespace TigerApi\Payload;

use TigerCore\ValueObject\VO_TokenPlainStr;

class TigerPayloadRefreshToken extends ATigerPayload {

  private \stdClass $payload;

  public function __construct(VO_TokenPlainStr $refreshTokenPlainStr){
    $this->payload = new \stdClass();
    $this->payload->refreshToken = $refreshTokenPlainStr->getValueAsString();
  }


  public function getPayloadRawData(): array|object
  {
    return $this->payload;
  }
}