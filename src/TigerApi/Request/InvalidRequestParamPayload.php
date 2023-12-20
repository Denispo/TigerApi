<?php

namespace TigerApi\Request;

use TigerApi\Payload\ATigerPayload;
use TigerCore\Response\S500_InternalServerErrorException;
use TigerCore\ValueObject\VO_PayloadKey;

class InvalidRequestParamPayload extends ATigerPayload {

  /**
   * @param string[] $invalidParam
   * @throws S500_InternalServerErrorException
   */
  public function __construct(array $invalidParam) {
     parent::__construct();
     $this->appendPayload($invalidParam, new VO_PayloadKey('errors'));
  }

  public function getPayloadKey(): VO_PayloadKey {
    return new VO_PayloadKey('perr');
  }
}