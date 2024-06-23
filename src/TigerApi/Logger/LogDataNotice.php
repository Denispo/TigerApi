<?php

namespace TigerApi\Logger;

class LogDataNotice extends BaseLogData {

  public function __construct(string $message, array $customData = [], IAmFileLineClass|null $fileLineClass = null) {
    if ($fileLineClass) {
      parent::__construct(
         message: $message,
         customData: $customData,
         file: $fileLineClass->getFile(),
         line: $fileLineClass->getLine(),
         class: $fileLineClass->getClass(),
         methodOrFunction: $fileLineClass->getMethodOrFunction()
      );
    } else {
      parent::__construct(
         message: $message,
         customData: $customData
      );
    }
  }

}