<?php

namespace TigerApi\Email;



use TigerCore\Exceptions\CanNotCloseFileException;
use TigerCore\Exceptions\CanNotOpenFileException;
use TigerCore\Exceptions\CanNotWriteToFileException;
use TigerCore\SafeFileStream;

class TigerMailToFileMailer implements ICanSendEmail {

  public function __construct(private string $fullFileName) {

  }

  /**
   * @param TigerMailMessage $mail
   * @return void
   * @throws CanNotSendEmailException
   */
  public function send(TigerMailMessage $mail): void {
    $stream = new SafeFileStream($this->fullFileName);
    $emailData = $mail->getSubject().PHP_EOL.print_r($mail->getHeaders() ?? [], true).PHP_EOL.PHP_EOL.$mail->getHtmlBody();
    try {
      $stream->addToFile($emailData);
    } catch (CanNotCloseFileException|CanNotOpenFileException|CanNotWriteToFileException $e) {
      throw new CanNotSendEmailException($e->getMessage(), $e->getCode(), $e);
    }
  }
}