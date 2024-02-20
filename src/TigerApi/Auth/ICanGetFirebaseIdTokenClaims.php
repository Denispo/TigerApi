<?php

namespace TigerApi\Auth;

use TigerCore\Auth\FirebaseIdTokenClaims;

interface ICanGetFirebaseIdTokenClaims
{
  public function getFirebaseIdTokenClaims():FirebaseIdTokenClaims;
}