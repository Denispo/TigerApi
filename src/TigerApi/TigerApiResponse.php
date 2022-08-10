<?php

namespace TigerApi;

use TigerCore\Payload\IBasePayload;
use TigerCore\Response\BaseResponse;
use TigerCore\Response\ICanGetPayload;

class TigerApiResponse extends BaseResponse implements ICanGetPayload {

  public function addPayload(IBasePayload $payload) {
    $key = $payload->getPayloadKey();
    if ($key->isValid()) {
      $this->payload[][$key->getValue()]['data'] = $payload->getPayloadData();
    }
  }

  public function getPayload(): array {
    return $this->payload;
  }

}