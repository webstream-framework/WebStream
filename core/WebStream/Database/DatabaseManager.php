<?php
namespace WebStream\Database;

use WebStream\Annotation\Container\AnnotationListContainer;
use WebStream\Module\Logger;
use WebStream\Exception\Extend\DatabaseException;

/**
 * DriverManager
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
     * @var boolean 接続エラーフラグ
     */
    private $isError;

    /**
     * constructor
     * @param AnnotationListContainer データベース接続項目コンテナ
     */
    public function __construct(AnnotationListContainer $connectionItemContainerList)
    {
        $this->connectionManager = new ConnectionManager($connectionItemContainerList);
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
        if (!$this->isError) {
            $this->connection->commit();
        }
        $this->connection->disconnect();
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

        $trans = $this->connection->beginTransaction();
        Logger::debug("Transaction start.");
    }

    /**
     * コミットする
     */
    public function commit()
    {
        $this->connection->commit();
        Logger::debug("Execute commit.");
    }

    /**
     * ロールバックする
     */
    public function rollback()
    {
        $this->connection->rollback();
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
        return $this->connection->inTransaction();
    }

    /**
     * DB接続されているか
     * @param boolean 接続有無
     */
    public function isConnected()
    {
        return $this->connection !== null && $this->connection->isConnected();
    }

    /**
     * データベース接続を使用する
     */
    public function useDatabase($filepath)
    {
        $this->connection = $this->connectionManager->getConnection($filepath);
        // DB接続がない場合は接続する
        if (!$this->isConnected()) {
            $this->connect();
            $this->beginTransaction();
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
