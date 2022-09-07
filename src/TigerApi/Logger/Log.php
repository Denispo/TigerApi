<?php

namespace TigerApi\Logger;

class Log {

  private static IAmBaseLogger $logger;

  private static int $errorCounter = 0;
  private static int $noticeCounter = 0;
  private static int $exceptionCounter = 0;
  private static int $warningCounter = 0;

  private static function _init(IAmBaseLogger $logger):void {
    self::$logger = $logger;
  }

  /**
   * @param string $message
   * @param array $customData
   * @param IAmFileLineClass|null $fileLineClass
   * @return void
   * @throws CyclicLoggerCallException
   */
  public static function Error(string $message, array $customData = [], IAmFileLineClass|null $fileLineClass = null):void {
    $logData = new LogDataError($message, $customData, $fileLineClass);
    if (self::$errorCounter > 0) {
      // Nekdo v self::$logger->logError($logData) zase zavolal Log::Error, takze jsme se zacyklili
      throw new CyclicLoggerCallException('Log::Error() was called again during logging first Log:Error call', $logData);
    }
    self::$errorCounter++;
    try {
      self::$logger->logError($logData);
    } finally {
      self::$errorCounter--;
    }
  }

  /**
   * @param string $message
   * @param array $customData
   * @param IAmFileLineClass|null $fileLineClass
   * @return void
   * @throws CyclicLoggerCallException
   */
  public static function Warning(string $message, array $customData = [], IAmFileLineClass|null $fileLineClass = null):void {
    $logData = new LogDataWarning($message, $customData, $fileLineClass);
    if (self::$warningCounter > 0) {
      // Nekdo v self::$logger->logWarning($logData) zase zavolal Log::Warning, takze jsme se zacyklili
      throw new CyclicLoggerCallException('Log::Warning() was called again during logging first Log:Warning call', $logData);
    }
    self::$warningCounter++;
    try {
      self::$logger->logWarning($logData);
    } finally {
      self::$warningCounter--;
    }
  }

  /**
   * @param string $message
   * @param array $customData
   * @param IAmFileLineClass|null $fileLineClass
   * @return void
   */
  public static function Notice(string $message, array $customData = [], IAmFileLineClass|null $fileLineClass = null):void {
    $logData = new LogDataNotice($message, $customData, $fileLineClass);
    if (self::$noticeCounter > 0) {
      // Nekdo v self::$logger->logNotice($logData) zase zavolal Log::Notice, takze jsme se zacyklili

      // U Notice ale Exception nevyhodime. Bo je to jen Notice

      //throw new CyclicLoggerCallException('Log::Notice() was called again during logging first Log:Notice call', $logData);
      return;
    }
    self::$noticeCounter++;
    try {
      self::$logger->logNotice($logData);
    } finally {
      self::$noticeCounter--;
    }
  }

  /**
   * @param \Throwable $exception
   * @param array $customData
   * @return void
   * @throws CyclicLoggerCallException
   */
  public static function Exception(\Throwable $exception, array $customData = []):void {
    $logData = new LogDataException($exception, $customData);
    if (self::$exceptionCounter > 0) {
      // Nekdo v self::$logger->LogException($logData) zase zavolal Log::Exception, takze jsme se zacyklili
      throw new CyclicLoggerCallException('Log::Exception() was called again during logging first Log:Exception call', $logData);
    }
    self::$exceptionCounter++;
    try {
      self::$logger->logException($logData);
    } finally {
      self::$exceptionCounter--;
    }
  }

}