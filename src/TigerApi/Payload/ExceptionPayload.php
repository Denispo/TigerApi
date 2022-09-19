<?php

namespace TigerApi\Payload;

use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\ValueObject\VO_PayloadKey;

class ExceptionPayload extends ATigerBasePayload {

  /**
   * @param ICanGetPayloadRawData|string $exceptionPayload
   */
  public function __construct(ICanGetPayloadRawData|string $exceptionPayload) {
    try {
      if (is_string($exceptionPayload)) {
        parent::__construct(['msg' => $exceptionPayload]);
      } else {
        parent::__construct(['payload' => $exceptionPayload->getPayloadRawData()]);
      }
    } catch (\ReflectionException $e) {
      // Toto by nemelo nikdy nastat, protoze mapFromDbData je vzdy false
    }

  }

  public function getPayloadKey(): VO_PayloadKey {
    return new VO_PayloadKey('error');
  }

}