<?php

namespace TigerApi\Payload;

use TigerCore\ValueObject\VO_PayloadKey;

class TigerPayloadRawData extends ATigerPayload {

  public function __construct(array|object $rawData, VO_PayloadKey $keyName)
  {
    $this->appendPayload($rawData, $keyName);
  }
}