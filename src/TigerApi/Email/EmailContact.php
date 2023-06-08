<?php

namespace TigerApi\Email;

use TigerCore\ValueObject\VO_Email;

class EmailContact
{
  public function __construct(
    private VO_Email $emailAddress,
    private string $contactName = ''
  )
  {

  }

  public function getEmailAddress(): VO_Email
  {
    return $this->emailAddress;
  }

  public function getContactName():string
  {
    return $this->contactName;
  }

}