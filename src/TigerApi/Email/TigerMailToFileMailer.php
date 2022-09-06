<?php

namespace TigerApi\Email;



use TigerCore\SafeFileStream;

class TigerMailToFileMailer implements ICanSendEmail {

  public function __construct(private string $fullFileName) {

  }

  public function send(TigerMailMessage $mail): void {
    $stream = new SafeFileStream($this->fullFileName);
    $emailData = $mail->getSubject().PHP_EOL.implode(PHP_EOL,$mail->getHeaders() ?? []).PHP_EOL.PHP_EOL.$mail->getHtmlBody();
    $stream->addToFile($emailData);
  }
}