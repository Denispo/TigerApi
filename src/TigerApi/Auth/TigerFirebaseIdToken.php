<?php

namespace TigerApi\Auth;

use Nette\Caching\Cache;
use TigerCore\Auth\FirebaseIdToken;
use TigerCore\Auth\FirebaseIdTokenClaims;
use TigerCore\Exceptions\InvalidArgumentException;
use TigerCore\Exceptions\InvalidFileNameException;
use TigerCore\Exceptions\InvalidFormatException;
use TigerCore\Exceptions\InvalidTokenException;
use TigerCore\ValueObject\VO_FullPathFileName;
use TigerCore\ValueObject\VO_TokenPlainStr;

class TigerFirebaseIdToken implements ICanGetFirebaseIdTokenClaims
{

  private FirebaseIdTokenClaims|null $claims = null;

  public function __construct(
    private readonly VO_TokenPlainStr    $firebaseIdToken,
    private readonly VO_FullPathFileName $fileNameFirebaseServiceAccountJson,
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

    $hash = substr(md5($this->fileNameFirebaseServiceAccountJson->getValueAsString()),0,5);
    $cacheKey = 'fb_idtoken_certificates_'.$hash;
    $certificates = $this->cache?->load($cacheKey)?? null;
    if ($certificates === null) {
      $certificates = @file_get_contents($this->fileNameFirebaseServiceAccountJson->getValueAsString());
      if ($certificates === false) {
        throw new InvalidFileNameException('Can not read service account file');
      }
      $certificates = json_decode($certificates,true);
      if ($certificates === null) {
        throw new InvalidFormatException('Invalid JSON format of service account file');
      }
      $certificates = @file_get_contents($certificates['client_x509_cert_url']);
      if ($certificates === false) {
        throw new InvalidFileNameException('Can not read service account certificates URL');
      }

    }
    $certificates = json_decode($certificates,true);
    // TODO: Set expiration time based on file_get_contents header response cache-control: max-age
    $this->cache?->save($cacheKey,$certificates,[Cache::Expire => time() + (5 * 60 * 60)]); // 5 hours.
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