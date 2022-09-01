<?php

namespace TigerApi\Logger;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;

class TigerFileLogger implements IAmBaseLogger {

  public function __construct(private string $pathToLogFolder) {

  }

  /**
   * @param string $fileName
   * @param string $data
   * @return void
   * @throws CanNotOpenLogFileException
   */
  private function addToFile(string $fileName, string $data) {
    $fullFilePath = FileSystem::joinPaths($this->pathToLogFolder, $fileName);
    $handle = fopen('nette.safe://'.$fullFilePath, 'a');
    if (!$handle) {
      throw new CanNotOpenLogFileException('Can not open Log file '.$fullFilePath, $data);
    }
    fwrite($handle,$data);
    fclose($handle);
  }

  private function formatLogData(BaseLogData $data): string {
    return PHP_EOL.(new DateTime())->format('h:i:s'.PHP_EOL.'d.m.Y').PHP_EOL.$data->getMessage().PHP_EOL.'FILE: '.$data->getFile().PHP_EOL.'CLASS: '.$data->getClass().PHP_EOL.'FUNCTION: '.$data->getMethodOrFunction().PHP_EOL.'LINE: '.$data->getLine().PHP_EOL.'DATA: '.print_r($data->getData(),true);
  }

  /**
   * @param LogDataError $logData
   * @return void
   * @throws CanNotOpenLogFileException
   */
  public function logError(LogDataError $logData):void {
    $this->addToFile('Error_'.(new DateTime())->format('Ymd').'.log', $this->formatLogData($logData));
  }

  /**
   * @param LogDataException $logData
   * @return void
   * @throws CanNotOpenLogFileException
   */
  public function logException(LogDataException $logData):void {
    $this->addToFile('Exception_'.(new DateTime())->format('Ymd').'.log', $this->formatLogData($logData));
  }

  /**
   * @param LogDataNotice $logData
   * @return void
   * @throws CanNotOpenLogFileException
   */
  public function logNotice(LogDataNotice $logData):void {
    $this->addToFile('Notice_'.(new DateTime())->format('Ymd').'.log', $this->formatLogData($logData));
  }

  /**
   * @param LogDataWarning $logData
   * @return void
   * @throws CanNotOpenLogFileException
   */
  public function logWarning(LogDataWarning $logData):void {
    $this->addToFile('Warning_'.(new DateTime())->format('Ymd').'.log', $this->formatLogData($logData));
  }
}