<?php

namespace TigerApi\Logger;

class LogDataException extends BaseLogData {

  public function __construct(\Throwable $exception, array $customData = []) {
    parent::__construct($exception->getMessage(),['CUSTOMDATA' => $customData,'EXCEPTION_TRACE' => $exception->getTrace()],$exception->getFile(),$exception->getLine());

  }

}