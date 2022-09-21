<?php

namespace TigerApi\Auth;

use TigerCore\Auth\ICanGetTokenPrivateKey;
use TigerCore\Auth\ICanGetTokenPublicKey;

interface IAmRefreshTokenFactory extends ICanGenerateRefreshTokenForUser , ICanGetTokenPublicKey, ICanGetTokenPrivateKey, ICanDecodeRefreshToken{

}