<?php

namespace TigerApi\Auth;

use TigerCore\Auth\FirebaseCustomToken;
use TigerCore\Auth\ICanGetTokenClaims;
use TigerCore\Exceptions\InvalidArgumentException;
use TigerCore\Exceptions\InvalidFileNameException;
use TigerCore\Exceptions\InvalidFormatException;
use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_FullPathFileName;
use TigerCore\ValueObject\VO_TokenPlainStr;

class FirebaseCustomTokenGenerator {

  /**
   * @param string|int $userId
   * @param VO_FullPathFileName|array{client_email:string,private_key:string} $serviceAccountData
   * @param ICanGetTokenClaims $tokenCustomClaims
   * @return VO_TokenPlainStr
   * @throws InvalidArgumentException
   * @throws InvalidFileNameException
   * @throws InvalidFormatException
   * @throws InvalidTokenException
   */
  public static function generate(string|int $userId,VO_FullPathFileName|array $serviceAccountData, ICanGetTokenClaims $tokenCustomClaims): VO_TokenPlainStr {
    if (empty($userId)) {
      throw new InvalidArgumentException('UserId can not be empty');
    }
    if ($serviceAccountData instanceof VO_FullPathFileName) {
      $serviceAccountData = @file_get_contents($serviceAccountData->getValueAsString());
      if ($serviceAccountData === false) {
        throw new InvalidFileNameException('Can not readservice account file');
      }
      $serviceAccountData = json_decode($serviceAccountData,true);
      if ($serviceAccountData === null) {
        throw new InvalidFormatException('Invalid JSON format of service account file');
      }
    }
    return FirebaseCustomToken::generateToken($serviceAccountData, $userId, $tokenCustomClaims);
  }

}