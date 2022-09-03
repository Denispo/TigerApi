<?php

namespace TigerApi\Logger;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use TigerApi\CanNotWriteToFileException;

class TigerFileLogger implements IAmBaseLogger {

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
    $errorMessage = '';
    $errorLine = 0;
    $errorFile = '';
    $errorCode = 0;
    $oldErrorHandler = null;
    if ($this->internalErrorHandler) {
      $oldErrorHandler = set_error_handler(function (int $errNo, string $errMsg, string $file, int $line) use (&$errorMessage, &$errorLine, &$errorFile, &$errorCode) {
        // Musime logovat jen prvni chybu. Viz komentar nize
        if ($errorMessage == '') {
          $errorMessage = $errMsg;
          $errorLine = $line;
          $errorFile = $file;
          $errorCode = $errNo;
        }
      });
    }

    // Pozor. @fopen('nette.safe://'.$fullFilePath, 'a'); zavola na pozadi SafeStream Wrapper od Nette a v nem se vola metoda stream_open, ktera taky vola svuj @fopen(.... Takze pokud v tomto Wrapperovskem @fopen dojde k chybe, zavola se nas internalErrorHandler poprve. Ale protoze tento Wrapper byl volany diky nasemu @fopen('nette.safe://'., tak tento nas fopen taky nasledne vzhodi chybu a takyznovu  zavola internalErrorHandler.
    // Napr. Wrapperovsky @fopen skonci chybou "Failed to open stream: Permission denied", ale tato informace se neprenese do naseho fopen, takze nas @fopen('nette.safe://'... skonci chybou "Failed to open stream: ... call failed"
    // Proto v internalErrorHandler logujeme jen prvni chybu (napr. permission denied), abychom vedeli, co se doopravdy stalo. Jinak bychom meli v $errorMessage vzdy chybu "call failed", ktera nam rekne prd.
    $handle = @fopen('nette.safe://'.$fullFilePath, 'a');
    if ($handle === false) {
      if ($this->internalErrorHandler && $oldErrorHandler) set_error_handler($oldErrorHandler);
      throw new CanNotWriteToFileException('Can not open Log file '.$fullFilePath.' Reason: '.$errorMessage, $data);
    }

    try {
      $errorMessage = '';
      $writeResult = @fwrite($handle,$data);
      if ($writeResult === false) {
        throw new CanNotWriteToFileException('Can not write to Log file '.$fullFilePath.' Reason: '.$errorMessage, $data);
      }
    } catch (\Throwable $e) {
      throw new CanNotWriteToFileException('Can not write to Log file '.$fullFilePath.' Reason: '.$errorMessage, $data);
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
   * @throws CanNotWriteToFileException
   */
  public function logError(LogDataError $logData):void {
    $this->addToFile('Error_'.(new DateTime())->format('Ymd').'.log', $this->formatLogData($logData));
  }

  /**
   * @param LogDataException $logData
   * @return void
   * @throws CanNotWriteToFileException
   */
  public function logException(LogDataException $logData):void {
    $this->addToFile('Exception_'.(new DateTime())->format('Ymd').'.log', $this->formatLogData($logData));
  }

  /**
   * @param LogDataNotice $logData
   * @return void
   * @throws CanNotWriteToFileException
   */
  public function logNotice(LogDataNotice $logData):void {
    $this->addToFile('Notice_'.(new DateTime())->format('Ymd').'.log', $this->formatLogData($logData));
  }

  /**
   * @param LogDataWarning $logData
   * @return void
   * @throws CanNotWriteToFileException
   */
  public function logWarning(LogDataWarning $logData):void {
    $this->addToFile('Warning_'.(new DateTime())->format('Ymd').'.log', $this->formatLogData($logData));
  }
}