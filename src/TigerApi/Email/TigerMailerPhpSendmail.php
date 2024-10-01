<?php

namespace TigerApi\Email;

use Nette\Mail\SendmailMailer;

class TigerMailerPhpSendmail extends SendmailMailer implements IAmTigerMailer{

   use CanSendAllEmailsOnlyToSpecificEmailAddress;

}