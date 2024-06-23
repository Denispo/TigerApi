<?php

namespace TigerApi\Logger;

class LogDataException extends BaseLogData {

  public function __construct(\Throwable $exception, array $customData = []) {
    parent::__construct(
       message: $exception->getMessage(),
       customData: $customData,
       exception: $exception,
       file: $exception->getFile(),
       line: $exception->getLine()
    );
  }

}