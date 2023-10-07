<?php

namespace TigerApi\Payload;

use TigerCore\Response\BaseResponseException;
use TigerCore\ValueObject\VO_PayloadKey;

class TigerPayloadException extends ATigerPayload {

  public function __construct(\Throwable $e) {
    $payload = new \stdClass();

    $payload->code = $e->getCode();
    $payload->message = $e->getMessage();
    $payload->file = $e->getFile();
    $payload->line = $e->getLine();
    $payload->customData = $e instanceof BaseResponseException ? $e->getCustomData() : [];

    $this->appendPayload($payload,new VO_PayloadKey('exception'));
  }

}