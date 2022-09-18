<?php

namespace TigerApi\Email;

interface ICanSendEmail {

  /**
   * @param TigerMailMessage $mail
   * @return void
   * @throws CanNotSendEmailException
   */
  public function send(TigerMailMessage $mail): void;
}