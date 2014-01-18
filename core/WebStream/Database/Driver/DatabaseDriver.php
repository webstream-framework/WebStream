<?php
namespace WebStream\Database\Driver;

use WebStream\Module\Logger;

/**
 * DatabaseDriver
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
abstract class DatabaseDriver
{
    /** connection */
    protected $connection;

    /** host */
    protected $host;

    /** port */
    protected $port;

    /** dbname */
    protected $dbname;

    /** user name */
    protected $username;

    /** password */
    protected $password;

    /** dbfile */
    protected $dbfile;

    /**
     * constructor
     */
    public function __construct()
    {
        Logger::debug("Load driver: " . get_class($this));
    }

    /**
     * destructor
     */
    public function __destruct()
    {
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
            $this->connection = null;
        }
    }

    /**
     * トランザクションを開始する
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * コミットする
     */
    public function commit()
    {
        $this->connection->commit();
    }

    /**
     * ロールバックする
     */
    public function rollback()
    {
        $this->connection->rollback();
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
     * SQLをセットしてステートメントを返却する
     * @param string SQL
     * @return object ステートメント
     */
    public function getStatement($sql)
    {
        return $this->connection->prepare($sql);
    }

    /**
     * ホスト名を設定する
     * @param string ホスト名
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * ポート番号を設定する
     * @param string ポート番号
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * DB名を設定する
     * @param string DB名
     */
    public function setDbname($dbname)
    {
        $this->dbname = $dbname;
    }

    /**
     * 接続ユーザ名を設定する
     * @param string 接続ユーザ名
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * パスワードを設定する
     * @param string パスワード
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * DBファイルを設定する
     * @param string DBファイル
     */
    public function setDbfile($dbfile)
    {
        $this->dbfile = $dbfile;
    }
}
