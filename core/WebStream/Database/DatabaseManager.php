<?php
namespace WebStream\Database;

use WebStream\Module\Logger;
use WebStream\Exception\Extend\DatabaseException;

/**
 * DatabaseManager
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
class DatabaseManager
{
    /**
     * @var ConnectionManager コネクションマネージャ
     */
    private $connectionManager;

    /**
     * @var DatabaseDriver データベースコネクション
     */
    private $connection;

    /**
     * @var Query クエリオブジェクト
     */
    private $query;

    /**
     * constructor
     * @param array<AnnotationContainer> データベース接続項目コンテナ
     */
    public function __construct(array $connectionItemContainerList)
    {
        $this->connectionManager = new ConnectionManager($connectionItemContainerList);
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
     * すでに接続中であれば再接続はしない
     */
    public function connect()
    {
        try {
            $this->connection->connect();
            $this->query = new Query($this->connection);
        } catch (\PDOException $e) {
            throw new DatabaseException($e);
        }
    }

    /**
     * データベース切断する
     */
    public function disconnect()
    {
        if ($this->connection === null) {
            return;
        }

        if ($this->inTransaction()) {
            $this->connection->commit();
        }

        $this->connection->disconnect();
        $this->connection = null;
        $this->query = null;
    }

    /**
     * トランザクションを開始する
     */
    public function beginTransaction()
    {
        // 既にトランザクションが開始されている場合、継続しているトランザクションを有効のままにする
        // トランザクションを破棄して再度開始する場合は明示的に破棄してから再呼び出しする
        if ($this->inTransaction()) {
            Logger::debug("Transaction already started.");

            return;
        }

        if (!$this->connection->beginTransaction()) {
            throw new DatabaseException("Failed to start transaction.");
        }

        Logger::debug("Transaction start.");
    }

    /**
     * コミットする
     */
    public function commit()
    {
        if ($this->inTransaction()) {
            $this->connection->commit();
            Logger::debug("Execute commit.");
        }

        $this->disconnect();
    }

    /**
     * ロールバックする
     */
    public function rollback()
    {
        try {
            $this->connection->rollback();
        } catch (\PDOException $e) {
            $this->query = null;
            $this->disconnect();
            throw new DatabaseException($e);
        }

        $this->query = null;
        $this->disconnect();
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
        return $this->connection->inTransaction();
    }

    /**
     * DB接続されているか
     * @param boolean 接続有無
     */
    public function isConnected()
    {
        return $this->connection->isConnected();
    }

    /**
     * データベース接続が可能かどうか
     * @param string Modelファイルパス
     * @return boolean 接続可否
     */
    public function loadConnection($filepath)
    {
        $connection = $this->connectionManager->getConnection($filepath);
        if ($connection !== null) {
            $this->connection = $connection;
        }

        return $this->connection !== null;
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
