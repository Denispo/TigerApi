<?php

namespace TigerApi;

use TigerCore\Payload\IBasePayload;
use TigerCore\Response\BaseResponse;
use TigerCore\Response\ICanGetPayloadData;

class TigerApiResponse extends BaseResponse implements ICanGetPayloadData {

  public function addPayload(IBasePayload $payload) {
    $key = $payload->getPayloadKey();
    if ($key->isValid()) {
      $this->payload[][$key->getValue()]['data'] = $payload->getPayloadData();
    }
  }

  public function getPayloadData(): array {
    return $this->payload;
  }

}