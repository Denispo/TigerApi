<?php

namespace TigerApi\Email;

use Nette\Mail\Mailer;
use Nette\Mail\SendException;
use Nette\Mail\SendmailMailer;

class TigerPhpMailer implements ICanSendEmail {

  private Mailer $mailer;

  public function __construct() {
    $this->mailer = new SendmailMailer();
  }

  /**
   * @param TigerMailMessage $mail
   * @return void
   * @throws CanNotSendEmailException
   */
  public function send(TigerMailMessage $mail): void {
    try {
      $this->mailer->send($mail);
    } catch (SendException $e) {
      throw new CanNotSendEmailException($e->getMessage(), $e->getCode(), $e);
    }

  }
}