<?php

namespace TigerApi\Database;

interface IAmMySqlConnectionCredentials {
  public function getHost():string;
  public function getDbName():string;
  public function getUserName():string;
  public function getPassword():string;

}