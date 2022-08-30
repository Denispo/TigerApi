<?php

namespace TigerApi\Email;

use Nette\Mail\SendException;

interface ICanSendEmail {

  /**
   * @param TigerMailMessage $mail
   * @return void
   * @throws SendException
   */
  public function send(TigerMailMessage $mail): void;
}