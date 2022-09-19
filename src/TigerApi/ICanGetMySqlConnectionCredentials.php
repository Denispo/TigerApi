<?php

namespace TigerApi;


interface ICanGetMySqlConnectionCredentials {
  public function getMySqlConnectionCredentials():IAmMySqlConnectionCredentials;

}