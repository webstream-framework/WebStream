<?php
namespace WebStream\Database;

use WebStream\Database\Driver\DatabaseDriver;
use WebStream\Database\Query;
use WebStream\Module\Logger;
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

    /** error flg */
    private $isError;

    /**
     * constructor
     * @param object データベースドライバ
     * @param array データベース設定
     */
    public function __construct(DatabaseDriver $driver, array $config)
    {
        $this->driver = $driver;
        $this->config = $config;
        $this->isError = false;
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * データベース接続する
     */
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
        if (array_key_exists("dbfile", $this->config)) {
            $this->driver->setDbfile($this->config["dbfile"]);
        }

        try {
            $this->driver->connect();
            $this->query = new Query($this->driver);

        } catch (\PDOException $e) {
            throw new DatabaseException($e);
        }
    }

    /**
     * データベース切断する
     */
    public function disconnect()
    {
        if (!$this->isError) {
            $this->driver->commit();
        }
        $this->driver->disconnect();
    }

    /**
     * トランザクションを開始する
     */
    public function beginTransaction()
    {
        // 既にトランザクションが開始されている場合、以前の処理を破棄して新たに開始
        if ($this->inTransaction()) {
            Logger::debug("Transaction destruction.");
            $this->rollback();
        }

        $trans = $this->driver->beginTransaction();
        Logger::debug("Transaction start.");
    }

    /**
     * コミットする
     */
    public function commit()
    {
        $this->driver->commit();
        Logger::debug("Execute commit.");
    }

    /**
     * ロールバックする
     */
    public function rollback()
    {
        $this->driver->rollback();
        $this->isError = true;
        Logger::debug("Execute rollback.");
    }

    /**
     * ロールバックが発生したかどうか
     * @return boolean ロールバックが発生したかどうか
     */
    public function isRollback()
    {
        return $this->isRollback;
    }

    /**
     * トランザクション内かどうか
     * @return boolean トランザクション内かどうか
     */
    public function inTransaction()
    {
        return $this->driver->inTransaction();
    }

    /**
     * クエリを設定する
     * @param string SQL
     * @param array<string> パラメータ
     */
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
