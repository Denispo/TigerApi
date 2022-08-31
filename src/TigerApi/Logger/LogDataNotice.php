<?php

namespace TigerApi\Logger;

class LogDataNotice extends BaseLogData {

  public function __construct(string $message, array $data, IAmFileLineClass|null $fileLineClass = null) {
    if ($fileLineClass) {
      parent::__construct($message, $data, $fileLineClass->getFile(), $fileLineClass->getLine(), $fileLineClass->getClass(), $fileLineClass->getMethodOrFunction());
    } else {
      parent::__construct($message, $data);
    }
  }

}