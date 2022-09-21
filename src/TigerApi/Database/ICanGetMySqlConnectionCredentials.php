<?php

namespace TigerApi\Database;


interface ICanGetMySqlConnectionCredentials {
  public function getMySqlConnectionCredentials():IAmMySqlConnectionCredentials;

}