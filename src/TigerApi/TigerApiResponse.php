<?php

namespace TigerApi;


use TigerApi\Payload\IAmTigerPayload;
use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\Response\BaseResponse;


class TigerApiResponse extends BaseResponse {

  public function addPayload(ICanGetPayloadRawData|IAmTigerPayload $payload):void {
    $this->payload[] = [
      'key' => $payload instanceof IAmTigerPayload ? $payload->getPayloadKey()->getValueAsString() : '',
      'data' => $payload->getPayloadRawData(),
    ];
  }

  public function getPayloadRawData(): array {
    return $this->payload;
  }
}