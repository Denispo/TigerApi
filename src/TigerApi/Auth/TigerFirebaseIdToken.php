<?php

namespace TigerApi\Auth;

use Nette\Caching\Cache;
use TigerCore\Auth\FirebaseIdToken;
use TigerCore\Auth\FirebaseIdTokenClaims;
use TigerCore\Exceptions\InvalidArgumentException;
use TigerCore\Exceptions\InvalidFileNameException;
use TigerCore\Exceptions\InvalidFormatException;
use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_TokenPlainStr;

class TigerFirebaseIdToken implements ICanGetFirebaseIdTokenClaims
{

  private FirebaseIdTokenClaims|null $claims = null;

  public function __construct(
    private readonly VO_TokenPlainStr    $firebaseIdToken,
    private readonly Cache|null          $cache = null,
  ){
  }


  /**
   * @return void
   * @throws InvalidFileNameException
   * @throws InvalidFormatException
   * @throws InvalidArgumentException
   * @throws InvalidTokenException|\Throwable
   */
  private function initializeIfNeeded():void
  {
    if ($this->claims !== null) {
      // already initialized;
      return;
    }

    $cacheKey = 'fb_idtoken_certificates';
    $certificates = $this->cache?->load($cacheKey)?? null;
    if ($certificates === null) {
      // https://firebase.google.com/docs/auth/admin/verify-id-tokens#verify_id_tokens_using_a_third-party_jwt_library
      $certificates = @file_get_contents('https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com');
      if ($certificates === false) {
        throw new InvalidFileNameException('Can not read service account certificates URL');
      }
      $certificates = json_decode($certificates,true);
      if ($certificates === null) {
        throw new InvalidFormatException('Downloaded certificates are in invalid format');
      }
    }

    try {
      // TODO: Set expiration time based on file_get_contents header response cache-control: max-age
      $this->cache?->save($cacheKey,$certificates,[Cache::Expire => time() + (5 * 60 * 60)]); // 5 hours.
    } catch (\Nette\InvalidArgumentException $e) {
      // TODO: Logovat nekde, ze cahce ma problem
    }

    $this->claims = FirebaseIdToken::decodeToken($this->firebaseIdToken, $certificates);
  }


  /**
   * @return FirebaseIdTokenClaims
   * @throws InvalidArgumentException
   * @throws InvalidFileNameException
   * @throws InvalidFormatException
   * @throws InvalidTokenException
   * @throws \Throwable
   */
  public function getFirebaseIdTokenClaims(): FirebaseIdTokenClaims
  {
    $this->initializeIfNeeded();
    return $this->claims;
  }
}