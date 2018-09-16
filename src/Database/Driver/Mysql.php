<?php
namespace WebStream\Database\Driver;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

/**
 * Mysql
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
class Mysql extends DatabaseDriver
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
            'driver'   => 'pdo_mysql',
            'charset'  => 'utf8'
        ];

        $config = new Configuration([
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
        ]);

        $this->connection = DriverManager::getConnection($params, $config);
        $this->logger->debug(get_class($this) . " connect.");
    }
}
