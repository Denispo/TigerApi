<?php

namespace TigerApi\Payload;

use TigerCore\ValueObject\VO_PayloadKey;

interface ICanGetPayloadKey {
  public function getPayloadKey(): VO_PayloadKey;

}