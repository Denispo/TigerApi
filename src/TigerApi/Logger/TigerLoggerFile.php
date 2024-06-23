<?php

namespace TigerApi\Logger;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use TigerCore\Exceptions\CanNotCloseFileException;
use TigerCore\Exceptions\CanNotOpenFileException;
use TigerCore\Exceptions\CanNotWriteToFileException;
use TigerCore\SafeFileStream;


class TigerLoggerFile implements IAmBaseLogger {


  public function __construct(private string $pathToLogFolder) {

  }


  /**
   * @param string $fileName
   * @param string $data
   * @throws CanNotWriteToFileException
   * @throws CanNotCloseFileException
   * @throws CanNotOpenFileException
   */
  private function addToFile(string $fileName, string $data):void {
    if ($data == '') {
      return;
    }
    $fullFilePath = FileSystem::joinPaths($this->pathToLogFolder, $fileName);
    $safeStream = new SafeFileStream($fullFilePath);
    $safeStream->addToFile($data);
  }

  private function formatLogData(BaseLogData $data): string {
    return PHP_EOL.(new DateTime())->format('h:i:s d.m.Y').PHP_EOL.$data->getMessage().PHP_EOL.'FILE: '.$data->getFile().PHP_EOL.'CLASS: '.$data->getClass().PHP_EOL.'FUNCTION: '.$data->getMethodOrFunction().PHP_EOL.'LINE: '.$data->getLine().PHP_EOL.'DATA: '.print_r($data->getCustomData(),true);
  }

  /**
   * @param LogDataError $logData
   * @return void
   * @throws CanNotCloseFileException
   * @throws CanNotOpenFileException
   * @throws CanNotWriteToFileException
   */
  public function logError(LogDataError $logData):void {
    $this->addToFile('Error_'.(new DateTime())->format('Ymd').'.log', $this->formatLogData($logData));
  }

  /**
   * @param LogDataException $logData
   * @return void
   * @throws CanNotCloseFileException
   * @throws CanNotOpenFileException
   * @throws CanNotWriteToFileException
   */
  public function logException(LogDataException $logData):void {
    $this->addToFile('Exception_'.(new DateTime())->format('Ymd').'.log', $this->formatLogData($logData));
  }

  /**
   * @param LogDataNotice $logData
   * @return void
   */
  public function logNotice(LogDataNotice $logData):void {
    try {
      $this->addToFile('Notice_' . (new DateTime())->format('Ymd') . '.log', $this->formatLogData($logData));
    } catch (CanNotWriteToFileException|CanNotCloseFileException|CanNotOpenFileException) {
      // u Notice nic nelogujeme
    }
  }

  /**
   * @param LogDataWarning $logData
   * @return void
   * @throws CanNotCloseFileException
   * @throws CanNotOpenFileException
   * @throws CanNotWriteToFileException
   */
  public function logWarning(LogDataWarning $logData):void {
    $this->addToFile('Warning_'.(new DateTime())->format('Ymd').'.log', $this->formatLogData($logData));
  }
}