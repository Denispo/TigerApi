<?php

namespace TigerApi\Logger;

class TigerFileLogger implements IBaseLogger {

  public function __construct(private string $pathToLogFolder) {

  }

  public function logError(BaseLogData $logData) {
    //$logData->
  }

  public function logException(LogDataException $logData) {
    // TODO: Implement logException() method.
  }

  public function logNotice(BaseLogData $logData) {
    // TODO: Implement logNotice() method.
  }

  public function logWarning(BaseLogData $logData) {
    // TODO: Implement logWarning() method.
  }
}