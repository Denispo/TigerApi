<?php

namespace TigerApi\Logger;

final class Log {

  private static ICanLogError $errorLogger;
  private static ICanLogWarning $warningLogger;
  private static ICanLogNotice $infoLogger;
  private static ICanLogException $exceptionLogger;

  public static function _init(ICanLogError $errorLogger, ICanLogWarning $warningLogger, ICanLogNotice $noticeLogger, ICanLogException $exceptionLogger):void {
    self::$errorLogger = $errorLogger;
    self::$warningLogger = $warningLogger;
    self::$infoLogger = $noticeLogger;
    self::$exceptionLogger = $exceptionLogger;
  }
  
  public static function Error(BaseLogData $logData):void {
    self::$errorLogger->logError($logData);
  }

  public static function Warning(BaseLogData $logData):void {
    self::$warningLogger->logWarning($logData);
  }

  public static function Notice(BaseLogData $logData):void {
    self::$infoLogger->logNotice($logData);
  }

  public static function Exception(\Throwable $exception):void {
    self::$exceptionLogger->logException($exception);
  }

}