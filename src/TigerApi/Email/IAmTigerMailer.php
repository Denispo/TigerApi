<?php

namespace TigerApi\Email;

use Nette\Mail\Message;
use Nette\Mail\SendException;
use TigerCore\ValueObject\VO_Email;

interface IAmTigerMailer
{
   /**
    * Sends email.
    * @throws SendException
    */
   function send(Message $mail): void;

   /**
    * Send all emails to $emailAddress instead of :standard: email address
    * @param VO_Email|null $emailAddress If set, all emails will be send to this email address only. Othervise normal behavior.
    * @return void
    */
   function sendAllEmailsOnlyToThisEmailAddress(VO_Email|null $emailAddress = null): void;
}