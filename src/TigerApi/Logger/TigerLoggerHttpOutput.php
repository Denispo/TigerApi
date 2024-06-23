<?php

namespace TigerApi\Logger;

class TigerLoggerHttpOutput implements IAmBaseLogger {

  public function __construct() {

  }


  private function formatLogData(BaseLogData $data): string {
    return $data->getMessage().PHP_EOL.'FILE: '.$data->getFile().PHP_EOL.'CLASS: '.$data->getClass().PHP_EOL.'FUNCTION: '.$data->getMethodOrFunction().PHP_EOL.'LINE: '.$data->getLine().PHP_EOL.'DATA: '.print_r($data->getCustomData(),true).PHP_EOL;
  }

  private function outData(string $data):void {
    $httpResponse = new \Nette\Http\Response();
    $httpResponse->setHeader('Access-Control-Allow-Origin','*');
    $httpResponse->setHeader('Access-Control-Allow-Headers','*');
    $httpResponse->setContentType('text/plain','utf-8');
    echo($data);
  }

  /**
   * @param LogDataError $logData
   * @return void
   */
  public function logError(LogDataError $logData):void {
    $this->outData('Error: '.$this->formatLogData($logData));
  }

  /**
   * @param LogDataException $logData
   * @return void
   */
  public function logException(LogDataException $logData):void {
    $this->outData('Exception: '.$this->formatLogData($logData));
  }

  /**
   * @param LogDataNotice $logData
   * @return void
   */
  public function logNotice(LogDataNotice $logData):void {
    $this->outData('Notice: '.$this->formatLogData($logData));
  }

  /**
   * @param LogDataWarning $logData
   * @return void
   */
  public function logWarning(LogDataWarning $logData):void {
    $this->outData('Warning: '.$this->formatLogData($logData));
  }
}