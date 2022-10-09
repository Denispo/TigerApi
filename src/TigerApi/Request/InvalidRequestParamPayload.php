<?php

namespace TigerApi\Request;

use TigerApi\Payload\ATigerBasePayload;
use TigerCore\ValueObject\VO_PayloadKey;

class InvalidRequestParamPayload extends ATigerBasePayload {

  /**
   * @param TigerInvalidRequestParam $invalidParam
   */
  public function __construct(TigerInvalidRequestParam $invalidParam) {
    try {
      parent::__construct(['name' => $invalidParam->paramName->getValue(), 'err_code' => $invalidParam->errorCode->getValue()]);
    } catch (\ReflectionException $e) {

    }
  }

  public function getPayloadKey(): VO_PayloadKey {
    return new VO_PayloadKey('perr');
  }
}