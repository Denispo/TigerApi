<?php

namespace TigerApi\Payload;

use TigerCore\ValueObject\VO_PayloadKey;
use TigerCore\ValueObject\VO_TokenPlainStr;

class TigerPayloadJwtToken extends ATigerPayload {

public function __construct(VO_TokenPlainStr $token){
  $this->appendPayload($token,new VO_PayloadKey('token'));
}
}