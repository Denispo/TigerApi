<?php

namespace TigerApi\Auth;

use TigerCore\Auth\BaseTokenClaims;
use TigerCore\Auth\FirebaseCustomToken;
use TigerCore\Auth\ICanAddTokenClaim;
use TigerCore\Exceptions\InvalidArgumentException;
use TigerCore\Exceptions\InvalidFileNameException;
use TigerCore\Exceptions\InvalidFormatException;
use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_FullPathFileName;
use TigerCore\ValueObject\VO_TokenPlainStr;

abstract class ATigerTokenJwtFirebaseCustomToken implements ICanGenerateFirebaseCustomToken {

  /**
   * @return VO_FullPathFileName|array{client_email:string,private_key:string}
   */
  protected abstract function onGetFirebaseServiceAccountJson():VO_FullPathFileName|array;
  protected abstract function onAddTokenCustomClaims(ICanAddTokenClaim $claimCollector):void;

  /**
   * @param string|int $userId
   * @return VO_TokenPlainStr
   * @throws InvalidArgumentException
   * @throws InvalidFileNameException
   * @throws InvalidTokenException
   * @throws InvalidFormatException
   */
  public function generateToken(string|int $userId): VO_TokenPlainStr {
    if (empty($userId)) {
      throw new InvalidArgumentException('UserId can not be empty');
    }
    $claims = new BaseTokenClaims();
    $this->onAddTokenCustomClaims($claims);
    $serviceAccountData = $this->onGetFirebaseServiceAccountJson();
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
    return FirebaseCustomToken::generateToken($serviceAccountData, $userId, $claims);
  }

}