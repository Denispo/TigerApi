<?php

namespace TigerApi;

use TigerCore\Payload\BasePayload;
use TigerCore\ValueObject\VO_PayloadKey;

class InvalidRequestParamPayload extends BasePayload {

  /**
   * @param TigerInvalidRequestParam $invalidParam
   */
  public function __construct(TigerInvalidRequestParam $invalidParam) {
    try {
      parent::__construct(['name' => $invalidParam->param->getParamName(), 'desc' => $invalidParam->description], false);
    } catch (\ReflectionException $e) {

    }
  }

  public function getPayloadKey(): VO_PayloadKey {
    return new VO_PayloadKey('perr');
  }
}