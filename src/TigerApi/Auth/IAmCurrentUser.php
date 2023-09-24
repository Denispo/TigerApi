<?php

namespace TigerApi\Auth;

interface IAmCurrentUser {

  public function isAuthenticated():bool;

  public function getUserId():int;

}