<?php

namespace TigerApi;

use TigerCore\Payload\IBasePayload;
use TigerCore\Response\BaseResponse;
use TigerCore\Response\ICanGetPayload;

class TigerApiResponse extends BaseResponse implements ICanGetPayload, ICanAddParamError {

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

  public function addParamError(ParamError $paramError) {
    $this->payload[]['p_error']['data'] = ['param' => $paramError->getParamName(), 'desc' => $paramError->getErrorDescription()];
  }
}