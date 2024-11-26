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

     // https://dev.mysql.com/doc/refman/8.4/en/sql-mode.html#sql-mode-strict
     // https://blog.andrewkoebbe.com/blog/2016-03-10.mysql-truncate
     // https://database.guide/how-to-remove-a-sql-mode-from-sql_mode-in-mysql/
     // https://mariadb.com/kb/en/list_drop/#:~:text=list_drop%20is%20a%20stored%20function,of%20options%2C%20such%20as%20sql_mode.
     // MySql from 5.7 throws exceptions when truncating and other values convertings on INSERT and UPDATE so we have to disable STRICT_TRANS_TABLES and STRICT_ALL_TABLES
     // $this->db->query("SET @@sql_mode = sys.list_drop(@@sql_mode, 'STRICT_TRANS_TABLES');");
  }

  public function GetDbConnection(): Connection {
    return $this->db;
  }
}