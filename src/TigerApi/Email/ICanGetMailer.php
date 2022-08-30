<?php

namespace TigerApi\Email;

use Nette\Mail\Mailer;

interface ICanGetMailer {
  public function getMailer():Mailer;
}