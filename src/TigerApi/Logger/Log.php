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
   * @param LogDataError $logData
   * @return void
   * @throws CyclicLoggerCallException
   */
  public static function Error(LogDataError $logData):void {
    if (self::$errorCounter > 0) {
      // Nekdo v self::$logger->logError($logData) zase zavolal Log::Error, takze jsme se zacyklili
      throw new CyclicLoggerCallException('Log::Error() was called again during logging first Log:Error call', $logData);
    }
    self::$errorCounter++;
    self::$logger->logError($logData);
    self::$errorCounter--;
  }

  /**
   * @param LogDataWarning $logData
   * @return void
   * @throws CyclicLoggerCallException
   */
  public static function Warning(LogDataWarning $logData):void {
    if (self::$warningCounter > 0) {
      // Nekdo v self::$logger->logWarning($logData) zase zavolal Log::Warning, takze jsme se zacyklili
      throw new CyclicLoggerCallException('Log::Warning() was called again during logging first Log:Warning call', $logData);
    }
    self::$warningCounter++;
    self::$logger->logWarning($logData);
    self::$warningCounter--;
  }

  /**
   * @param LogDataNotice $logData
   * @return void
   * @throws CyclicLoggerCallException
   */
  public static function Notice(LogDataNotice $logData):void {
    if (self::$noticeCounter > 0) {
      // Nekdo v self::$logger->logNotice($logData) zase zavolal Log::Notice, takze jsme se zacyklili
      throw new CyclicLoggerCallException('Log::Notice() was called again during logging first Log:Notice call', $logData);
    }
    self::$noticeCounter++;
    self::$logger->logNotice($logData);
    self::$noticeCounter--;
  }

  /**
   * @param LogDataException $logData
   * @return void
   * @throws CyclicLoggerCallException
   */
  public static function Exception(LogDataException $logData):void {
    if (self::$noticeCounter > 0) {
      // Nekdo v self::$logger->LogException($logData) zase zavolal Log::Exception, takze jsme se zacyklili
      throw new CyclicLoggerCallException('Log::Exception() was called again during logging first Log:Exception call', $logData);
    }
    self::$exceptionCounter++;
    self::$logger->logException($logData);
    self::$exceptionCounter--;
  }

}