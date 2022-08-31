<?php

namespace TigerApi\Logger;

class TigerFileLogger implements IBaseLogger {

  public function __construct(private string $pathToLogFolder) {

  }

  public function logError(LogDataError $logData) {
    //$logData->
  }

  public function logException(LogDataException $logData) {
    // TODO: Implement logException() method.
  }

  public function logNotice(LogDataNotice $logData) {
    // TODO: Implement logNotice() method.
  }

  public function logWarning(LogDataWarning $logData) {
    // TODO: Implement logWarning() method.
  }
}