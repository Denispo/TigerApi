<?php

namespace TigerApi\Email;

use Nette\Mail\Mailer;
use Nette\Mail\SendmailMailer;

class TigerPhpMailer implements ICanSendEmail {

  private Mailer $mailer;

  public function __construct() {
    $this->mailer = new SendmailMailer();
  }

  public function getMailer(): Mailer {
    return $this->mailer;
  }

  public function send(TigerMailMessage $mail): void {
    $this->mailer->send($mail);
  }
}