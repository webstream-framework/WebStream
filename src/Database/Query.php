<?php
namespace WebStream\Database;

use WebStream\DI\Injector;
use WebStream\Database\Driver\DatabaseDriver;
use WebStream\Exception\Extend\DatabaseException;

/**
 * Query
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.7
 */
class Query
{
    use Injector;

    /**
     * @var DatabaseDriver データベースコネクション
     */
    private $connection;

    /**
     * @var string SQL
     */
    private $sql;

    /**
     * @var array<mixed> バインドパラメータ
     */
    private $bind;

    /**
     * @var Doctrine\DBAL\Statement ステートメント
     */
    private $stmt;

    /**
     * Constructor
     * @param DatabaseDriver データベースコネクション
     */
    public function __construct(DatabaseDriver $connection)
    {
        $this->connection = $connection;
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
        $this->logger->debug("execute select.");
        $this->execute();
        $result = new Result($this->stmt);
        $result->inject('logger', $this->logger);

        return $result;
    }

    /**
     * INSERT
     * @return integer 処理結果件数
     */
    public function insert()
    {
        $this->logger->debug("execute insert.");

        return $this->execute();
    }

    /**
     * UPDATE
     * @return integer 処理結果件数
     */
    public function update()
    {
        $this->logger->debug("execute update.");

        return $this->execute();
    }

    /**
     * DELETE
     * @return integer 処理結果件数
     */
    public function delete()
    {
        $this->logger->debug("execute delete.");

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
            $stmt = $this->connection->getStatement($this->sql);
            if ($stmt === false) {
                throw new DatabaseException("Can't create statement: ". $this->sql);
            }
            $this->logger->info("Executed SQL: " . $this->sql);
            foreach ($this->bind as $key => $value) {
                $this->logger->info("Bind statement: $key => $value");
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
        } catch (\Exception $e) {
            throw new DatabaseException($e);
        }
    }
}
