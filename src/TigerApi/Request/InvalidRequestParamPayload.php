<?php

namespace TigerApi\Request;

use TigerApi\Payload\ATigerBasePayload;
use TigerCore\Response\S500_InternalServerErrorException;
use TigerCore\ValueObject\VO_PayloadKey;

class InvalidRequestParamPayload extends ATigerBasePayload {

  /**
   * @param TigerInvalidRequestParam $invalidParam
   * @throws S500_InternalServerErrorException
   */
  public function __construct(TigerInvalidRequestParam $invalidParam) {
     parent::__construct(['name' => $invalidParam->paramName->getValueAsString(), 'err_code' => $invalidParam->errorCode->getValueAsString()]);
  }

  public function getPayloadKey(): VO_PayloadKey {
    return new VO_PayloadKey('perr');
  }
}