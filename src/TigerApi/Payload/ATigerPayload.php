<?php

namespace TigerApi\Payload;

use TigerCore\ICanGetValueAsBoolean;
use TigerCore\ICanGetValueAsFloat;
use TigerCore\ICanGetValueAsInit;
use TigerCore\ICanGetValueAsString;
use TigerCore\ICanGetValueAsTimestamp;
use TigerCore\ValueObject\VO_PayloadKey;

abstract class ATigerPayload  implements IAmTigerPayload {

  private array $payload = [];

  /**
   * @param array|string|int|float|object|ICanGetValueAsInit|ICanGetValueAsString|ICanGetValueAsBoolean|ICanGetValueAsFloat|ICanGetValueAsTimestamp $data
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
    }
    $payload = new \stdClass();
    $key = $key->getValueAsString();
    $payload->$key = $data;
    $this->payload[] = $payload;
  }

  public function getPayloadRawData(): array|object
  {
    return $this->payload;
  }

}