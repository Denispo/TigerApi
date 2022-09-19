<?php

namespace TigerApi;

use Nette\Database\Connection;
use TigerCore\Repository\ICanGetDbConnection;

class TigerMysqlDb implements ICanGetDbConnection {

  private Connection $db;

  public function __construct(IAmMySqlConnectionCredentials $connectionCredentials) {
    $this->db = new Connection('mysql:host='.$connectionCredentials->getHost().';dbname='.$connectionCredentials->getDbName(), $connectionCredentials->getUserName(), $connectionCredentials->getPassword(),['lazy']);
  }

  public function GetDbConnection(): Connection {
    return $this->db;
  }
}