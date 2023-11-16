<?php

namespace TigerApi\Request;

use TigerApi\Payload\ATigerPayload;
use TigerCore\Response\S500_InternalServerErrorException;
use TigerCore\ValueObject\VO_PayloadKey;

class InvalidRequestParamPayload extends ATigerPayload {

  /**
   * @param TigerInvalidRequestParam $invalidParam
   * @throws S500_InternalServerErrorException
   */
  public function __construct(TigerInvalidRequestParam $invalidParam) {
     parent::__construct();
     $this->appendPayload($invalidParam->paramName->getValueAsString(), new VO_PayloadKey('name'));
     $this->appendPayload($invalidParam->errorCode->getValueAsString(), new VO_PayloadKey('err_code'));
  }

  public function getPayloadKey(): VO_PayloadKey {
    return new VO_PayloadKey('perr');
  }
}