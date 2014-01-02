<?php
namespace WebStream\Database\Driver;

use WebStream\Module\Logger;

/**
 * Mysql
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
class Mysql extends DatabaseDriver
{
    /**
     * Override
     */
    public function connect()
    {
        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname;
        $dsn.= $this->port !== null ? ";port=" . $this->port : "";
        $username = $this->username;
        $password = $this->password;
        $options = [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    \PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true];

        $this->connection = new \PDO($dsn, $username, $password, $options);
        Logger::debug("Database connect.");
    }
}
