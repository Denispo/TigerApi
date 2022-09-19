<?php

namespace TigerApi\Payload;

use TigerCore\ValueObject\VO_PayloadKey;

class AuthTokenPayload extends ATigerTokenPayload {

  public function getPayloadKey(): VO_PayloadKey {
    return new VO_PayloadKey('tkn_auth');
  }


}