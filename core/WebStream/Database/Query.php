<?php
namespace WebStream\Database;

use WebStream\Module\Logger;
use WebStream\Database\Driver\DatabaseDriver;
use WebStream\Exception\Extend\DatabaseException;

/**
 * Query
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
class Query
{
    /** データベースドライバ */
    private $driver;

    /** SQL */
    private $sql;

    /** バインドパラメータ */
    private $bind;

    /** ステートメント */
    private $stmt;

    /**
     * Constructor
     * @param object データベースドライバ
     */
    public function __construct(DatabaseDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * SQLを設定する
     * @param string SQL
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
    }

    /**
     * バインドパラメータを設定する
     * @param array<string> バインドパラメータ
     */
    public function setBind(array $bind)
    {
        $this->bind = $bind;
    }

    /**
     * SELECT
     * @return object 取得結果
     */
    public function select()
    {
        Logger::debug("execute select.");
        $this->execute();

        return new Result($this->stmt);
    }

    /**
     * INSERT
     * @return integer 処理結果件数
     */
    public function insert()
    {
        Logger::debug("execute insert.");

        return $this->execute();
    }

    /**
     * UPDATE
     * @return integer 処理結果件数
     */
    public function update()
    {
        Logger::debug("execute update.");

        return $this->execute();
    }

    /**
     * DELETE
     * @return integer 処理結果件数
     */
    public function delete()
    {
        Logger::debug("execute delete.");

        return $this->execute();
    }

    /**
     * SQLを実行する
     * @return integer 結果件数
     */
    private function execute()
    {
        $this->stmt = null;

        try {
            $stmt = $this->driver->getStatement($this->sql);
            if ($stmt === false) {
                throw new DatabaseException("Can't create statement: ". $this->sql);
            }
            Logger::info("Executed SQL: " . $this->sql);
            foreach ($this->bind as $key => $value) {
                Logger::info("Bind statement: $key => $value");
                if (preg_match("/^[0-9]+$/", $value) && is_int($value)) {
                    $stmt->bindValue($key, $value, \PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value, \PDO::PARAM_STR);
                }
            }

            if ($stmt->execute()) {
                $this->stmt = $stmt;
                $rowCount = $stmt->rowCount();

                return $rowCount;
            } else {
                $messages = $stmt->errorInfo();
                $message = $messages[2];
                $sqlState = "(SQL STATE: ${messages[0]})";
                $errorCode = "(ERROR CODE: ${messages[1]})";
                throw new DatabaseException("${message} ${sqlState} ${errorCode}");
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e);
        }
    }
}
