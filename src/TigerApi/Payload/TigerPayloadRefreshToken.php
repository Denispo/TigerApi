<?php

namespace TigerApi\Payload;

use TigerCore\ValueObject\VO_PayloadKey;
use TigerCore\ValueObject\VO_TokenPlainStr;

class TigerPayloadRefreshToken extends ATigerPayload {

  public function __construct(VO_TokenPlainStr $refreshTokenPlainStr){
    parent::__construct();
    $payload = new \stdClass();
    $payload->token = $refreshTokenPlainStr->getValueAsString();
    $this->appendPayload($payload, new VO_PayloadKey('token'));
  }

}