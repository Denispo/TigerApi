<?php

namespace TigerApi\Email;

use Nette\Mail\Message;
use TigerCore\Exceptions\InvalidArgumentException;
use TigerCore\ValueObject\VO_Email;

class TigerMailMessage implements IAmTigerMessage {

  private Message $message;

  public function __construct()
  {
    $this->message = new Message();
    $this->message::$defaultHeaders = [
      'MIME-Version' => '1.0',
//      'X-Mailer' => 'Nette Framework',
    ];
  }

  public function setSubject(string $subject): void
  {
    $this->message->setSubject($subject);
  }

  public function getSubject(): string
  {
    return $this->message->getSubject();
  }

  public function setFrom(EmailContact $contact): void
  {
    $this->message->setFrom($contact->getEmailAddress()->getValueAsString(), $contact->getContactName());
  }

  public function getFrom(): EmailContact|null
  {
    $from = $this->message->getFrom();
    if ($from === null) {
      return null;
    }
    try {
      return new EmailContact(new VO_Email(array_key_first($from)), $from[0]);
    } catch (InvalidArgumentException) {
      return null;
    }
  }

  public function clearTo():void
  {
    $this->message->clearHeader('To');
  }

  public function addTo(EmailContact $to): void
  {
    $this->message->addTo($to->getEmailAddress()->getValueAsString(), $to->getContactName());
  }

  /**
   * @return EmailContact[]
   * @throws InvalidArgumentException
   */
  public function getTo(): array
  {
    $result = [];
    $tos = $this->message->getHeader('To');
    if (!$tos) {
      $tos = [];
    }
    foreach ($tos as $to) {
      $result[] = new EmailContact(new VO_Email(array_key_first($to)), $to[0]);
    }
    return $result;
  }

   public function setBody(string $body): void
  {
    $this->message->setBody($body);
  }

  public function getBody(): string
  {
    return $this->message->getBody();
  }

  public function setHtmlBody(string $htmlBody, string $basePath = ''): void
  {
    $this->message->setHtmlBody($htmlBody, $basePath);
  }

  public function getHtmlBody(): string
  {
    return $this->message->getHtmlBody();
  }

  public function generateMessage(): string
  {
    return $this->message->generateMessage();
  }

}