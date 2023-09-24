<?php

namespace TigerApi\Payload;

class TigerPayloadRawData extends ATigerPayload {

  public function __construct(private readonly array|\stdClass $rawData = [])
  {
  }

  public function getPayloadRawData(): array|\stdClass
  {
    return $this->rawData;
  }
}