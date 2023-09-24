<?php

namespace TigerApi\Auth;

interface ICanGetCurrentUser {

  public function getCurrentUser():IAmCurrentUser;

}