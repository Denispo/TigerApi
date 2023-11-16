<?php

namespace TigerApi\Payload;

use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\ValueObject\VO_PayloadKey;

class TigerPayloadRawData extends ATigerPayload {

  public function __construct(array|\stdClass|ICanGetPayloadRawData $rawData, VO_PayloadKey $keyName)
  {
    parent::__construct();
    $this->appendPayload($rawData, $keyName);
  }
}