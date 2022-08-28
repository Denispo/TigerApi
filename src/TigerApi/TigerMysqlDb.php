<?php

namespace TigerApi;

use Nette\Database\Connection;
use TigerCore\Repository\ICanGetDbConnection;

class TigerMysqlDb implements ICanGetDbConnection {

  private Connection $db;

  public function __construct(string $host, string $dbName, string $user, string $password) {
    $this->db = new Connection('mysql:host='.$host.';dbname='.$dbName, $user, $password,['lazy']);
  }

  public function GetDbConnection(): Connection {
    return $this->db;
  }
}