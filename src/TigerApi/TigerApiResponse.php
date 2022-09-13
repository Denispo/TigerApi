<?php

namespace TigerApi;


use TigerApi\Payload\IAmTigerPayload;
use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\Response\BaseResponse;


class TigerApiResponse extends BaseResponse implements ICanGetPayloadRawData {

  public function addPayload(ICanGetPayloadRawData|IAmTigerPayload $payload) {
    $this->payload[] = [
      'key' => $payload instanceof IAmTigerPayload ? $payload->getPayloadKey()->getValue() : '',
      'data' => $payload->getPayloadRawData(),
    ];
  }

  public function getPayloadRawData(): array {
    return $this->payload;
  }
}