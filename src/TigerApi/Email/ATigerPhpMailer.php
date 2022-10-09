<?php

namespace TigerApi\Email;

use Nette\Mail\Mailer;
use Nette\Mail\SendException;
use Nette\Mail\SendmailMailer;
use TigerCore\Email\BaseMailer;
use TigerCore\Email\BaseMailMessage;
use TigerCore\Email\CanNotSendEmailException;

abstract class ATigerPhpMailer extends BaseMailer {

  private Mailer $mailer;

  protected abstract function onAfterEmailHasBeenSent(BaseMailMessage $mail);


  public function __construct() {
    $this->mailer = new SendmailMailer();
  }

  /**
   * @param BaseMailMessage $mailMessage
   * @return void
   * @throws CanNotSendEmailException
   */
  public function send(BaseMailMessage $mailMessage): void {
    try {
      $this->mailer->send($mailMessage);
    } catch (SendException $e) {
      throw new CanNotSendEmailException($e->getMessage(), $e->getCode(), $e);
    }
    $this->onAfterEmailHasBeenSent($mailMessage);
  }
}