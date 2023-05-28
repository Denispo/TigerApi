<?php

namespace TigerApi\Payload;

use TigerCore\ValueObject\VO_TokenPlainStr;

abstract class ATigerTokenPayload extends ATigerBasePayload {

  public function __construct(VO_TokenPlainStr $tokenStr) {
    parent::__construct(['tkn' => $tokenStr->getValueAsString()]);
  }

}