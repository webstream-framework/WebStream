<?php
namespace WebStream\Database\Driver;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

/**
 * Sqlite
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
class Sqlite extends DatabaseDriver
{
    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        $params = [
            'path'     => $this->config->dbfile,
            'user'     => $this->config->username,
            'password' => $this->config->password,
            'driver'   => 'pdo_sqlite',
            'charset'  => 'utf8'
        ];

        $config = new Configuration([
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);

        $this->connection = DriverManager::getConnection($params, $config);
        $this->logger->debug(get_class($this) . " connect.");
    }
}
