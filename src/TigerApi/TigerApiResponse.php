<?php

namespace TigerApi;

use TigerCore\Payload\IBasePayload;
use TigerCore\Response\BaseResponse;
use TigerCore\Response\ICanGetPayload;

class TigerApiResponse extends BaseResponse implements ICanGetPayload {

  public function addToPayload(IBasePayload $payload) {
    $key = $payload->getPayloadKey();
    if ($key->isValid()) {
      $this->payload[][$key->getValue()]['data'] = $payload->getPayloadData();
    }
  }
/*
  public function addError(ResponseError $responseError, string $description = '') {
    $this->payload[]['error']['data'] = ['errno' => $responseError->getValue(), 'desc' => $description];
  }*/

  public function getPayload(): array {
    return $this->payload;
  }
}