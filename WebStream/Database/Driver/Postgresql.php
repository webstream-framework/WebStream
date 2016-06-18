<?php
namespace WebStream\Database\Driver;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

/**
 * Postgresql
 * @author Ryuichi TANAKA.
 * @since 2014/01/03
 * @version 0.4
 */
class Postgresql extends DatabaseDriver
{
    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        $params = [
            'dbname'   => $this->config->dbname,
            'user'     => $this->config->username,
            'password' => $this->config->password,
            'host'     => $this->config->host,
            'port'     => $this->config->port,
            'driver'   => 'pdo_pgsql',
            'charset'  => 'utf8'
        ];

        $config = new Configuration([\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
        $this->connection = DriverManager::getConnection($params, $config);

        $this->logger->debug(get_class($this) . " connect.");
    }
}
