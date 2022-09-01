<?php

namespace TigerApi\Logger;

use TigerCore\Exceptions\_BaseException;

class CanNotOpenLogFileException extends _BaseException {

  public function __construct(string $message, public string $dataWhichFailedToBeLogged) {
    parent::__construct($message);
  }


}