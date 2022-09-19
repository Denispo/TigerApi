<?php

namespace TigerApi;

use TigerCore\Auth\ICanGetTokenPrivateKey;
use TigerCore\Auth\ICanGetTokenPublicKey;

interface IAmTokenFactory extends ICanGenerateRefreshTokenForUser , ICanGenerateAuthTokenForUser, ICanGetTokenPublicKey, ICanGetTokenPrivateKey, ICanDecodeRefreshToken, ICanDecodeAuthToken{

}