<?php

namespace TigerApi\Logger;


class TigerLoggerSentry implements IAmBaseLogger {


   private const string SEVERITY_NOTICE = 'notice';
   private const string SEVERITY_WARNING = 'warning';
   private const string SEVERITY_ERROR = 'error';

  public function __construct() {

  }

   /**
    * @param LogDataException $logData
    * @return void
    */
   public function logException(LogDataException $logData): void
   {
      $captureExceptionFunction = '\Sentry\captureException';
      $eventHintClass   = '\Sentry\EventHint';

      // Sentry je volitelná závislost → voláme dynamicky, aby statická analýza neřvala
      if (function_exists($captureExceptionFunction) && class_exists($eventHintClass)) {
         $captureExceptionFunction(
            $logData->getException(),
            $eventHintClass::fromArray(['extra' => $logData->getCustomData()])
         );
      }
   }

   private function sentryMessage(BaseLogData $logData, string $severity):void
   {
      $extra['customData'] = $logData->getCustomData();
      $extra['file'] = $logData->getFile();
      $extra['line'] = $logData->getLine();
      $extra['class'] = $logData->getClass();
      $extra['method'] = $logData->getMethodOrFunction();

      $captureExceptionFunction = '\Sentry\captureException';
      $eventHintClass   = '\Sentry\EventHint';
      $severityClass   = '\Sentry\Severity';

      // Sentry je volitelná závislost → voláme dynamicky, aby statická analýza neřvala
      if (class_exists($severityClass)) {
         switch ($severity) {
            case self::SEVERITY_WARNING:{
               $severity = new $severityClass($severityClass::WARNING);
               break;
            }
            case self::SEVERITY_NOTICE:{
               $severity = new $severityClass($severityClass::INFO);
               break;
            }
            case self::SEVERITY_ERROR:{
               $severity = new $severityClass($severityClass::ERROR);
               break;
            }
            default:{
               $severity = null;
            }
         }
      }
      if (function_exists($captureExceptionFunction) && class_exists($eventHintClass)) {
         $captureExceptionFunction($logData->getMessage(), $severity, $eventHintClass::fromArray(['extra' => $extra]));
      }

   }

   /**
    * @param LogDataError $logData
    * @return void
    */
   public function logError(LogDataError $logData):void {
      $this->sentryMessage($logData, self::SEVERITY_ERROR);
   }

  /**
   * @param LogDataNotice $logData
   * @return void
   */
  public function logNotice(LogDataNotice $logData):void {
     $this->sentryMessage($logData, self::SEVERITY_NOTICE);
  }

  /**
   * @param LogDataWarning $logData
   * @return void
   */
  public function logWarning(LogDataWarning $logData):void {
     $this->sentryMessage($logData, self::SEVERITY_WARNING);
  }
}