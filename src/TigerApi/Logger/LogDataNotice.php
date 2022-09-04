<?php

namespace TigerApi\Logger;

class LogDataNotice extends BaseLogData {

  public function __construct(string $message, array $customData = [], IAmFileLineClass|null $fileLineClass = null) {
    if ($fileLineClass) {
      parent::__construct($message, $customData, $fileLineClass->getFile(), $fileLineClass->getLine(), $fileLineClass->getClass(), $fileLineClass->getMethodOrFunction());
    } else {
      parent::__construct($message, $customData);
    }
  }

}