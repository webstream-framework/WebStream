<?php
namespace WebStream\Database;

use WebStream\DI\Injector;
use WebStream\Container\Container;
use WebStream\Exception\Extend\DatabaseException;
use Doctrine\DBAL\Connection;

/**
 * DatabaseManager
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
class DatabaseManager
{
    use Injector;

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
     * @var Logger ロガー
     */
    private $logger;

    /**
     * constructor
     * @param Container 依存コンテナ
     */
    public function __construct(Container $container)
    {
        $this->connectionManager = new ConnectionManager($container);
        $this->logger = $container->logger;
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
            $this->query->inject('logger', $this->logger);
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
            // トランザクションが明示的に開始された状態でcommit/rollbackが行われていない場合
            // ログに警告を書き込み、強制的にロールバックする
            $this->connection->rollback();
            $this->logger->warn("Not has been executed commit or rollback after the transaction started.");
        }

        $this->connection->disconnect();
        $this->connection = null;
        $this->query = null;
    }

    /**
     * トランザクションを開始する
     * @param int $isolationLevel トランザクション分離レベル
     */
    public function beginTransaction(int $isolationLevel)
    {
        // 既にトランザクションが開始されている場合、継続しているトランザクションを有効のままにする
        // トランザクションを破棄して再度開始する場合は明示的に破棄してから再呼び出しする
        if ($this->inTransaction()) {
            $this->logger->debug("Transaction already started.");

            return;
        }

        if (!$this->connection->beginTransaction()) {
            throw new DatabaseException("Failed to start transaction.");
        }

        if ($isolationLevel === Connection::TRANSACTION_READ_UNCOMMITTED ||
            $isolationLevel === Connection::TRANSACTION_READ_COMMITTED ||
            $isolationLevel === Connection::TRANSACTION_REPEATABLE_READ ||
            $isolationLevel === Connection::TRANSACTION_SERIALIZABLE) {
            $this->connection->setTransactionIsolation($isolationLevel);
        } else {
            throw new DatabaseException("Invalid transaction isolation level: " . $isolationLevel);
        }

        $this->logger->debug("Transaction start.");
    }

    /**
     * コミットする
     */
    public function commit()
    {
        try {
            if ($this->connection !== null) {
                if ($this->inTransaction()) {
                    $this->connection->commit();
                    $this->logger->debug("Execute commit.");
                } else {
                    $this->logger->warn("Not executed commit because the transaction is not started.");
                }
            } else {
                throw new DatabaseException("Can't execute commit.");
            }
        } catch (\PDOException $e) {
            $this->query = null;
            throw new DatabaseException($e);
        } finally {
            $this->disconnect();
        }
    }

    /**
     * ロールバックする
     */
    public function rollback()
    {
        try {
            if ($this->connection !== null) {
                if ($this->inTransaction()) {
                    $this->connection->rollback();
                    $this->logger->debug("Execute rollback.");
                } else {
                    $this->logger->warn("Not executed rollback because the transaction is not started.");
                }
            } else {
                throw new DatabaseException("Can't execute rollback.");
            }
        } catch (\PDOException $e) {
            $this->query = null;
            throw new DatabaseException($e);
        } finally {
            $this->disconnect();
        }
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
     * トランザクション分離レベルを返却する
     * @return int トランザクション分離レベル
     */
    public function getTransactionIsolation()
    {
        return $this->connection->getTransactionIsolation();
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
