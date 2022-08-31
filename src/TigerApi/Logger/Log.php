<?php

namespace TigerApi\Logger;

class Log {

  /**
   * @var ICanLogError|ICanLogError[]
   */
  private static ICanLogError|array $errorLogger = [];
  /**
   * @var ICanLogWarning|ICanLogWarning[]
   */
  private static ICanLogWarning|array $warningLogger = [];
  /**
   * @var ICanLogNotice|ICanLogNotice[]
   */
  private static ICanLogNotice|array $infoLogger = [];
  /**
   * @var ICanLogException|ICanLogException[]
   */
  private static ICanLogException|array $exceptionLogger = [];

  /**
   * If array, all loggers within array will be called sequentialy
   * @param ICanLogError|ICanLogError[] $errorLogger
   * @param ICanLogWarning|ICanLogWarning[] $warningLogger
   * @param ICanLogNotice|ICanLogNotice[] $noticeLogger
   * @param ICanLogException|ICanLogException[] $exceptionLogger
   * @return void
   */
  public static function _init(ICanLogError|array $errorLogger, ICanLogWarning|array $warningLogger, ICanLogNotice|array $noticeLogger, ICanLogException|array $exceptionLogger):void {
    self::$errorLogger = is_array($errorLogger) ? $errorLogger : [$errorLogger];
    self::$warningLogger = is_array($warningLogger) ? $warningLogger : [$warningLogger];;
    self::$infoLogger = is_array($noticeLogger) ? $noticeLogger : [$noticeLogger];;
    self::$exceptionLogger = is_array($exceptionLogger) ? $exceptionLogger : [$exceptionLogger];;
  }

  public static function Error(BaseLogData $logData):void {
    foreach (self::$errorLogger as $oneLogger) {
      $oneLogger->logError($logData);
    }
  }

  public static function Warning(BaseLogData $logData):void {
    foreach (self::$warningLogger as $oneLogger) {
      $oneLogger->logWarning($logData);
    }
  }

  public static function Notice(BaseLogData $logData):void {
    foreach (self::$infoLogger as $oneLogger) {
      $oneLogger->logNotice($logData);
    }
  }

  public static function Exception(\Throwable $exception):void {
    foreach (self::$exceptionLogger as $oneLogger) {
      $oneLogger->logException($exception);
    }
  }

}