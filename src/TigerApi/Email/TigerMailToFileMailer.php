<?php

namespace TigerApi\Email;

use Nette\Mail\Mailer;
use Nette\Mail\SendmailMailer;
use TigerApi\CanNotOpenFileException;

class TigerMailToFileMailer implements ICanSendEmail {

  public function __construct(private string $fullFileName) {

  }

  public function send(TigerMailMessage $mail): void {
    $handle = @fopen($this->fullFileName, 'a');
    if (!$handle) {
     // throw new CanNotOpenFileException()
    }
  }
}