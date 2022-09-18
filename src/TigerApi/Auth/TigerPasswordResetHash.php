<?php

namespace TigerApi\Auth;

use TigerCore\Exceptions\ExpiredException;
use TigerCore\Exceptions\InvalidArgumentException;
use TigerCore\Utils\Crypt;
use TigerCore\ValueObject\VO_BaseId;
use TigerCore\ValueObject\VO_Duration;

class TigerPasswordResetHash {

  public function __construct(private string $passphrase) {

  }

  public function generateHashForPasswordReset(VO_BaseId $userId):string {
    // Time is rouded to 5 minute granularity (to save some bytes)
    $timestampPacked = pack('V',round(ceil(time() / 60*5)));

    // $timestampPacked is 4 Bytes long there. We can remove first byte, because it is always 0 (because time() / (60*5))
    $timestampPacked = substr($timestampPacked, 1);

    $userId = $userId->getValue();
    if (is_int($userId)) {
      $userIdPacked = pack('l', $userId); // 32 bit unsigned integer
    } else {
      $userIdPacked = $userId;
      if (strlen($userIdPacked) === 4) {
        // Length of $userIdPacked can not be 4 bytes. 4 bytes length is "reserved" for integer version of UserId. Because pack('l', $userId); always returns 4 bytes length string.
        // If length of $userIdPacked is accidentaly 4 bytes, we have to add one additional byte to recognize that $userIdPacked is string.
        $userIdPacked = $userIdPacked . "\x00";
      }
    }
    $hash = Crypt::encode($timestampPacked.$userIdPacked, $this->passphrase);
    return $hash;
  }

  /**
   * @param string $passwordResetHash
   * @param VO_Duration $hashDuration
   * @return VO_BaseId
   * @throws InvalidArgumentException|ExpiredException
   */
  public function getUserIdFromPasswordResetHash(string $passwordResetHash, VO_Duration $hashDuration):VO_BaseId {
    $binary = Crypt::decode($passwordResetHash, $this->passphrase);
    if (strlen($binary) < 4) {
      // 3 bytes timestamp + 1 byte (at least) UserId
      throw new InvalidArgumentException();
    }
    $timestamp = current(unpack('V',"\x00".substr($binary,0,3))) * (60*5);

    if (($timestamp + $hashDuration->getValue() < time()) || ($timestamp - (5*60) > time())) {
      // $timestamp - (5*60) can not be bigger than time()
      throw new ExpiredException();
    }

    $userId = substr($binary, 3);

    if (strlen($userId) === 4) {
      // $userId is an 32 bit integer
      $userId = current(unpack('l', $userId));
    } else {
      // $userId is a string
      $userId = rtrim($userId,"\x00");
    }

    return new VO_BaseId($userId);

  }

}