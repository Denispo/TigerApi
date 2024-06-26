<?php

namespace TigerApi\Payload;

use TigerCore\ICanGetValueAsBoolean;
use TigerCore\ICanGetValueAsFloat;
use TigerCore\ICanGetValueAsInit;
use TigerCore\ICanGetValueAsString;
use TigerCore\ICanGetValueAsTimestamp;
use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\ValueObject\VO_PayloadKey;

class TigerPayload  implements IAmTigerPayload {

  private \stdClass $payload;

  public function __construct()
  {
    $this->payload = new \stdClass();
  }

  /**
   * @param array|string|int|float|object|ICanGetValueAsInit|ICanGetValueAsString|ICanGetValueAsBoolean|ICanGetValueAsFloat|ICanGetValueAsTimestamp|ICanGetPayloadRawData $data
   * @param VO_PayloadKey $key
   * @return void
   */
  protected function appendPayload(mixed $data, VO_PayloadKey $key):void
  {
    if ($data instanceof ICanGetValueAsInit) {
      $data = $data->getValueAsInt();
    } elseif ($data instanceof ICanGetValueAsString) {
      $data = $data->getValueAsString();
    } elseif ($data instanceof ICanGetValueAsBoolean) {
      $data = $data->getValueAsBool();
    } elseif ($data instanceof ICanGetValueAsFloat) {
      $data = $data->getValueAsFloat();
    } elseif ($data instanceof ICanGetValueAsTimestamp) {
      $data = $data->getValueAsTimestamp();
    } elseif ($data instanceof ICanGetPayloadRawData) {
      $data = $data->getPayloadRawData();
    }
    $key = $key->getValueAsString();
    $this->payload->$key = $data;
  }

  public function getPayloadRawData(): array|object
  {
    return $this->payload;
  }

}