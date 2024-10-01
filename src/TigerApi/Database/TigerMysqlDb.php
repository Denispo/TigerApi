<?php

namespace TigerApi\Database;

use Nette\Database\Connection;
use TigerCore\Repository\ICanGetDbConnection;

class TigerMysqlDb implements ICanGetDbConnection {

  private Connection $db;

  public function __construct(IAmMySqlConnectionCredentials $connectionCredentials) {
    // \PDO::MYSQL_ATTR_FOUND_ROWS => TRUE means: UPDATE command will return affectedRows = 1 even if new and current value has the same value i.e. nothing is actually updated.
    // Default behavior is UPDATE return 0 AffectedRows if new value has the same value as current value stored in DB.
    // This behavior cause unability to deretmine if record in DV was not found, or was found but contains already contains new value
    $this->db = new Connection('mysql:host='.$connectionCredentials->getHost().';dbname='.$connectionCredentials->getDbName(), $connectionCredentials->getUserName(), $connectionCredentials->getPassword(),['lazy' => true, \PDO::MYSQL_ATTR_FOUND_ROWS => true]);

    // Ach boze... nette :/
    // https://github.com/nette/database/issues/257#issuecomment-1016559714
    // $this->db->setRowNormalizer((new RowNormalizer())->skipNumeric());
    // S novym Nette je setRowNormalizer deprecated s hlaskou: "() is deprecated, configure 'convert*' options instead."
    // Takze vlastne nam rekl hovno. Tak snad je tento zapis 1:1 tomu prdeslemu :/
     $this->db->getTypeConverter()->convertDecimal = false;
  }

  public function GetDbConnection(): Connection {
    return $this->db;
  }
}