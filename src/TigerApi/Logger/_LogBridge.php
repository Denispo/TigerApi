<?php

namespace TigerApi\Logger;

class _LogBridge implements IAmBaseLogger {

  /**
   * @var callable
   */
  private $onLogError;
  /**
   * @var callable
   */
  private $onLogWarning;
  /**
   * @var callable
   */
  private $onLogNotice;
  /**
   * @var callable
   */
  private $onLogException;

  public function __construct(callable $onLogError, callable $onLogWarning, callable $onLogNotice, callable $onLogException) {

    $this->onLogError = $onLogError;
    $this->onLogWarning = $onLogWarning;
    $this->onLogNotice = $onLogNotice;
    $this->onLogException = $onLogException;
  }

  public function logError(LogDataError $logData) {
    ($this->onLogError)($logData);
  }

  public function logException(LogDataException $logData) {
    ($this->onLogException)($logData);
  }

  public function logNotice(LogDataNotice $logData) {
    ($this->onLogNotice)($logData);
  }

  public function logWarning(LogDataWarning $logData) {
    ($this->onLogWarning)($logData);
  }
}