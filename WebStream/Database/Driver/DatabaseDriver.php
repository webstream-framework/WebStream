<?php
namespace WebStream\Database\Driver;

use WebStream\Module\Logger;
use WebStream\Module\Container;

/**
 * DatabaseDriver
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
abstract class DatabaseDriver
{
    /**
     * @var object DBオブジェクト
     */
    protected $connection;

    /**
     * @var Container DB接続設定
     */
    protected $config;

    /**
     * constructor
     */
    public function __construct(Container $config)
    {
        $this->config = $config;
        Logger::debug("Load driver: " . get_class($this));
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        Logger::debug("Release driver: " . get_class($this));
    }

    /**
     * 接続する
     */
    abstract public function connect();

    /**
     * 切断する
     */
    public function disconnect()
    {
        if ($this->connection !== null) {
            Logger::debug("Database disconnect.");
            $this->connection->close();
            $this->connection = null;
        }
    }

    /**
     * トランザクションを開始する
     * @return boolean トランザクション開始結果
     */
    public function beginTransaction()
    {
        $inTransaction = false;
        if ($this->connection !== null) {
            $this->connection->beginTransaction();
            $inTransaction = $this->inTransaction();
        }

        return $inTransaction;
    }

    /**
     * コミットする
     */
    public function commit()
    {
        if ($this->connection !== null) {
            $this->connection->commit();
        }
    }

    /**
     * ロールバックする
     */
    public function rollback()
    {
        if ($this->inTransaction()) {
            $this->connection->rollback();
        }
    }

    /**
     * DB接続されているか
     * @param boolean 接続有無
     */
    public function isConnected()
    {
        return $this->connection !== null;
    }

    /**
     * トランザクション内かどうか
     * @return boolean トランザクション内かどうか
     */
    public function inTransaction()
    {
        return $this->connection !== null ? $this->connection->isTransactionActive() : false;
    }

    /**
     * SQLをセットしてステートメントを返却する
     * @param string SQL
     * @return object ステートメント
     */
    public function getStatement($sql)
    {
        return $this->connection !== null ? $this->connection->prepare($sql) : null;
    }
}
