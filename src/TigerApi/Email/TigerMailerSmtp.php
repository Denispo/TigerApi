<?php

namespace TigerApi\Email;

use Nette\Mail\SmtpMailer;

class TigerMailerSmtp extends SmtpMailer implements IAmTigerMailer{

   use CanSendAllEmailsOnlyToSpecificEmailAddress;

}