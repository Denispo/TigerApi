<?php

namespace TigerApi\Payload;

class TigerPayloadRawData extends ATigerPayload {

  public function __construct(private readonly array|object $rawData = [])
  {
  }

  public function getPayloadRawData(): array|object
  {
    return $this->rawData;
  }
}