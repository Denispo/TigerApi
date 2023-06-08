<?php

namespace TigerApi\Email;

use TigerCore\ValueObject\VO_Email;

interface IAmTigerMessage
{

  public function setSubject(string $subject): void;

  public function getSubject(): string;

  public function setFrom(EmailContact $contact): void;

  public function getFrom(): EmailContact|null;

  public function clearTo():void;
  public function addTo(EmailContact $to): void;

  public function getTo(): array;

  public function setBody(string $body): void;

  public function getBody(): string;

  public function setHtmlBody(string $htmlBody, string $basePath = ''): void;

  public function getHtmlBody(): string;

  public function generateMessage(): string;
}