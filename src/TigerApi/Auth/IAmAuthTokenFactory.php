<?php

namespace TigerApi\Auth;

use TigerCore\Auth\ICanGetTokenPrivateKey;
use TigerCore\Auth\ICanGetTokenPublicKey;

interface IAmAuthTokenFactory extends ICanGenerateAuthTokenForUser, ICanGetTokenPublicKey, ICanGetTokenPrivateKey, ICanDecodeAuthToken{

}