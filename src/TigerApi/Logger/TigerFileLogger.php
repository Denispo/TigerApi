<?php

namespace TigerApi\Logger;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use TigerApi\CanNotOpenFileException;
use TigerApi\CanNotWriteToFileException;

class TigerFileLogger implements IAmBaseLogger {

   public string $errorMessage = '';
  public int $errorLine = 0;
  public string $errorFile = '';
  public string $errorCode = '';

  public function __construct(private string $pathToLogFolder, private $internalErrorHandler = true) {

  }

  /**
   * @param string $fileName
   * @param string $data
   * @return void
   * @throws CanNotWriteToFileException
   */
  private function addToFile(string $fileName, string $data) {
    $fullFilePath = FileSystem::joinPaths($this->pathToLogFolder, $fileName);
    $this->errorMessage = '';
    $this->errorLine = 0;
    $this->errorFile = '';
    $this->errorCode = '';
    if ($this->internalErrorHandler) {
      $oldErrorHandler = set_error_handler(function (int $errNo, string $errMsg, string $file, int $line) {
        $this->errorMessage = $errMsg;
        $this->errorLine = $line;
        $this->errorFile = $file;
        $this->errorCode = $errNo;
      });
    }
    $handle = @fopen('nette.safe://'.$fullFilePath, 'a');
    if ($handle === false) {
      if ($this->internalErrorHandler) set_error_handler($oldErrorHandler);
      throw new CanNotWriteToFileException("Wow my custom error handler got #[$this->errorCode] occurred in [$this->errorFile] at line [$this->errorLine]: [$this->errorMessage]", $data);
      //throw new CanNotWriteToFileException("Can not open Log file '.$fullFilePath.' Reason: '.$errorMessage, $data);
    }

    try {
      $writeResult = @fwrite($handle,$data);
      if ($writeResult === false) {
        throw new CanNotWriteToFileException('Can not write to Log file '.$fullFilePath.' Reason: '.$this->errorMessage, $data);
      }
    } catch (\Throwable $e) {
      throw new CanNotWriteToFileException('Can not write to Log file '.$fullFilePath.' Reason: '.$this->errorMessage, $data);
    } finally {
      @fclose($handle);
      if ($this->internalErrorHandler) set_error_handler($oldErrorHandler);
    }

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