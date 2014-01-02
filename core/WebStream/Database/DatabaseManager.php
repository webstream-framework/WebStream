<?php
namespace WebStream\Database;

use WebStream\Database\Driver\DatabaseDriver;
use WebStream\Database\Query;
use WebStream\Exception\DatabaseException;

/**
 * DriverManager
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
class DatabaseManager
{
    /** database driver */
    private $driver;

    /** query */
    private $query;

    /** database config */
    private $config;

    /**
     * constructor
     * @param object データベースドライバ
     * @param array データベース設定
     */
    public function __construct(DatabaseDriver $driver, array $config)
    {
        $this->driver = $driver;
        $this->config = $config;
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    public function connect()
    {
        if (array_key_exists("host", $this->config)) {
            $this->driver->setHost($this->config["host"]);
        }
        if (array_key_exists("port", $this->config)) {
            $this->driver->setPort($this->config["port"]);
        }
        if (array_key_exists("dbname", $this->config)) {
            $this->driver->setDbname($this->config["dbname"]);
        }
        if (array_key_exists("username", $this->config)) {
            $this->driver->setUsername($this->config["username"]);
        }
        if (array_key_exists("password", $this->config)) {
            $this->driver->setPassword($this->config["password"]);
        }

        try {
            $this->driver->connect();
            $this->query = new Query($this->driver);

        } catch (\PDOException $e) {
            throw new DatabaseException($e);
        }
    }

    public function disconnect()
    {
        $this->driver->disconnect();
    }

    public function query($sql, array $bind = [])
    {
        if ($this->query === null) {
            throw new DatabaseException("Query does not set because database connection failed.");
        }
        $this->query->setSql($sql);
        $this->query->setBind($bind);

        return $this->query;
    }

}
