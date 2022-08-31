<?php

namespace TigerApi\Logger;

class Log {

  private static IAmBaseLogger $logger;

  private static function _init(IAmBaseLogger $logger):void {
    self::$logger = $logger;
  }

  public static function Error(BaseLogData $logData):void {
    self::$logger->logError($logData);
  }

  public static function Warning(BaseLogData $logData):void {
    self::$logger->logWarning($logData);
  }

  public static function Notice(BaseLogData $logData):void {
    self::$logger->logNotice($logData);
  }

  public static function Exception(\Throwable $exception):void {
    self::$logger->logException($exception);
  }

}