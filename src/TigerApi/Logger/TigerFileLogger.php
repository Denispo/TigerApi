<?php

namespace TigerApi\Logger;

class TigerFileLogger extends BaseLogger implements ICanLogError {

  public function __construct(private string $path) {

  }

  public function logError(BaseLogData $logData) {
    //$logData->
  }
}