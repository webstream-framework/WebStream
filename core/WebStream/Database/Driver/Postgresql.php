<?php
namespace WebStream\Database\Driver;

use WebStream\Module\Logger;

/**
 * Postgresql
 * @author Ryuichi TANAKA.
 * @since 2014/01/03
 * @version 0.4
 */
class Postgresql extends DatabaseDriver
{
    /**
     * Override
     */
    public function connect()
    {
        $dsn = "pgsql:host=" . $this->host . ";dbname=" . $this->dbname;
        $dsn.= $this->port !== null ? ";port=" . $this->port : "";
        $username = $this->username;
        $password = $this->password;
        $options = [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION];
        $this->connection = new \PDO($dsn, $username, $password, $options);
        Logger::debug("PostgreSQL connect.");
    }
}
