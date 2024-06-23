<?php

namespace TigerApi\Logger;

use TigerCore\Exceptions\BaseTigerException;

class CyclicLoggerCallException extends BaseTigerException {

  public function __construct(string $message, public BaseLogData $calledFrom) {
    parent::__construct($message);
  }


}