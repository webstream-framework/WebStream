<?php
namespace WebStream\Database\Driver;

use WebStream\Module\Logger;

/**
 * Sqlite
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
class Sqlite extends DatabaseDriver
{
    /**
     * Override
     */
    public function connect()
    {
        $dsn = "sqlite:" . $this->dbfile;
        $username = $this->username;
        $password = $this->password;
        $options = [\PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION];
        $this->connection = new \PDO($dsn, $username, $password, $options);
        Logger::debug("Database connect.");
    }
}
