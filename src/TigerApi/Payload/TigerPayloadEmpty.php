<?php

namespace TigerApi\Payload;

class TigerPayloadEmpty implements IAmTigerPayload {

  private static \stdClass $payload;

  public function __construct()
  {
    self::$payload = new \stdClass();
  }

  public function getPayloadRawData(): array|object
  {
    return self::$payload;
  }
}