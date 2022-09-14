<?php

namespace TigerApi\Request;

use TigerApi\Payload\TigerPayload;
use TigerCore\ValueObject\VO_PayloadKey;

class InvalidRequestParamPayload extends TigerPayload {

  /**
   * @param TigerInvalidRequestParam $invalidParam
   */
  public function __construct(TigerInvalidRequestParam $invalidParam) {
    try {
      parent::__construct(['name' => $invalidParam->param->getParamName(), 'desc' => $invalidParam->description]);
    } catch (\ReflectionException $e) {

    }
  }

  public function getPayloadKey(): VO_PayloadKey {
    return new VO_PayloadKey('perr');
  }
}