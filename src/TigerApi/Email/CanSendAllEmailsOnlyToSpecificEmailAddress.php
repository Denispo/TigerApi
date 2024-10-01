<?php

namespace TigerApi\Email;

use Nette\Mail\Message;
use TigerCore\ValueObject\VO_Email;

trait CanSendAllEmailsOnlyToSpecificEmailAddress{

   protected VO_Email|null $sendAllEmailThere = null;

   function sendAllEmailsOnlyToThisEmailAddress(?VO_Email $emailAddress = null): void
   {
      $this->sendAllEmailThere = $emailAddress;
   }

   public function send(Message $mail): void
   {
      if ($this->sendAllEmailThere) {
         $mail->clearHeader('To');
         $mail->addTo($this->sendAllEmailThere->getValueAsString());
      }
      parent::send($mail);
   }
}