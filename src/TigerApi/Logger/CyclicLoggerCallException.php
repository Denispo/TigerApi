<?php

namespace TigerApi\Logger;

use TigerCore\Exceptions\_BaseException;

class CyclicLoggerCallException extends _BaseException {

  public function __construct(string $message, public BaseLogData $calledFrom) {
    parent::__construct($message);
  }


}