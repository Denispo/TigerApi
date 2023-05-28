<?php

namespace TigerApi\Payload;

use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\Response\S500_InternalServerErrorException;
use TigerCore\ValueObject\VO_PayloadKey;

class ExceptionPayload extends ATigerBasePayload {

  /**
   * @param ICanGetPayloadRawData|string $exceptionPayload
   * @throws S500_InternalServerErrorException
   */
  public function __construct(ICanGetPayloadRawData|string $exceptionPayload) {
    if (is_string($exceptionPayload)) {
      parent::__construct(['msg' => $exceptionPayload]);
    } else {
      parent::__construct(['payload' => $exceptionPayload->getPayloadRawData()]);
    }
  }

  public function getPayloadKey(): VO_PayloadKey {
    return new VO_PayloadKey('error');
  }

}