<?php

namespace TigerApi\Email;

use TigerCore\Email\BaseMailer;
use TigerCore\Email\BaseMailMessage;
use TigerCore\Email\CanNotSendEmailException;
use TigerCore\Exceptions\CanNotCloseFileException;
use TigerCore\Exceptions\CanNotOpenFileException;
use TigerCore\Exceptions\CanNotWriteToFileException;
use TigerCore\SafeFileStream;

abstract class ATigerMailToFileMailer extends BaseMailer {

  protected abstract function onAfterEmailHasBeenSavedToFile(BaseMailMessage $mail, string $fileName);

  /** Return empty string or null to use default formating.
   * Returning non-empty string, it will be saved to the file as it is (no additional formating will be applied).
   * @param BaseMailMessage $mail
   * @return string|null
   */
  protected abstract function onFormatEmailMessage(BaseMailMessage $mail):string|null;


  public function __construct(private string $fullFileName) {

  }

  /**
   * @param BaseMailMessage $mailMessage
   * @return void
   * @throws CanNotSendEmailException
   */
  public function send(BaseMailMessage $mailMessage): void {
    $stream = new SafeFileStream($this->fullFileName);

    $emailData = $this->onFormatEmailMessage($mailMessage);
    if (!$emailData) {
      $emailData = $mailMessage->getSubject().PHP_EOL.print_r($mailMessage->getHeaders() ?? [], true).PHP_EOL.PHP_EOL.$mailMessage->getHtmlBody();
    }
    try {
      $stream->addToFile($emailData);
    } catch (CanNotCloseFileException|CanNotOpenFileException|CanNotWriteToFileException $e) {
      throw new CanNotSendEmailException($e->getMessage(), $e->getCode(), $e);
    }

    $this->onAfterEmailHasBeenSavedToFile($mailMessage, $this->fullFileName);
  }
}