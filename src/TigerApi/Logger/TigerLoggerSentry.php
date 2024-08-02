<?php

namespace TigerApi\Logger;


class TigerLoggerSentry implements IAmBaseLogger {


   private const SEVERITY_NOTICE = 'notice';
   private const SEVERITY_WARNING = 'warning';
   private const SEVERITY_ERROR = 'error';

  public function __construct() {

  }

  /**
   * @param LogDataException $logData
   * @return void
   */
  public function logException(LogDataException $logData):void {
     if (function_exists('\Sentry\captureException') && class_exists('\Sentry\EventHint')) {
        \Sentry\captureException($logData->getException(),\Sentry\EventHint::fromArray(['extra' => $logData->getCustomData()]));
     }
  }

   private function sentryMessage(BaseLogData $logData, string $severity)
   {
      $extra['customData'] = $logData->getCustomData();
      $extra['file'] = $logData->getFile();
      $extra['line'] = $logData->getLine();
      $extra['class'] = $logData->getClass();
      $extra['method'] = $logData->getMethodOrFunction();
      if (class_exists('\Sentry\Severity')) {
         switch ($severity) {
            case self::SEVERITY_WARNING:{
               $severity = new \Sentry\Severity(\Sentry\Severity::WARNING);
               break;
            }
            case self::SEVERITY_NOTICE:{
               $severity = new \Sentry\Severity(\Sentry\Severity::INFO);
               break;
            }
            case self::SEVERITY_ERROR:{
               $severity = new \Sentry\Severity(\Sentry\Severity::ERROR);
               break;
            }
            default:{
               $severity = null;
            }
         }
      }
      if (function_exists('\Sentry\captureMessage') && class_exists('\Sentry\EventHint')) {
         \Sentry\captureMessage($logData->getMessage(), $severity, \Sentry\EventHint::fromArray(['extra' => $extra]));
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