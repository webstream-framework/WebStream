<?php
namespace WebStream\Core;

use WebStream\Module\Container;
use WebStream\Module\Logger;
use WebStream\Database\DatabaseManager;
use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Annotation\Reader\DatabaseReader;
use WebStream\Annotation\Reader\QueryReader;
use WebStream\Exception\Extend\DatabaseException;
use WebStream\Exception\Extend\MethodNotFoundException;

/**
 * CoreModel
 * @author Ryuichi TANAKA.
 * @since 2012/09/01
 * @version 0.4
 */
class CoreModel implements CoreInterface
{
    /** database manager */
    private $manager;

    /** query reqder */
    private $queryReader;

    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        Logger::debug("Model start.");
        $this->initialize($container);
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        Logger::debug("Model end.");
    }

    /**
     * 初期処理
     * @param Container DIコンテナ
     */
    private function initialize(Container $container)
    {
        $reader = new AnnotationReader($this);
        $reader->setContainer($container);
        $reader->read();

        $database = new DatabaseReader($reader);
        $database->execute();
        $driver = $database->getDriver();
        $config = $database->getConfig();

        // driverまたはconfigが取れない場合、ModelでDB接続しない
        if ($driver === null || $config === null) {
            Logger::warn("Can't use database in Model Layer.");

            return;
        }

        $this->manager = new DatabaseManager($database->getDriver(), $database->getConfig());
        $this->manager->connect();
        $this->manager->beginTransaction();

        $query = new QueryReader($reader);
        $query->execute();
        $this->queryReader = $query;
    }

    /**
     * method missing
     * @param string メソッド名
     * @param array sql/bindパラメータ
     */
    public function __call($method, $arguments)
    {
        // DBを使用しない場合にここが呼ばれたらエラー
        if ($this->manager === null) {
            throw new MethodNotFoundException("Undefined method called: $method");
        }

        $sql = null;
        $bind = null;
        $result = null;

        try {
            if (preg_match('/^(?:select|(?:dele|upda)te|insert)$/', $method)) {
                $sql = $arguments[0];
                if (array_key_exists(1, $arguments)) {
                    $bind = $arguments[1];
                }
                switch ($method) {
                    case "select":
                        if (is_string($sql)) {
                            if (is_array($bind)) {
                                $result = $this->manager->query($sql, $bind)->select();
                            } else {
                                $result = $this->manager->query($sql)->select();
                            }
                        } else {
                            throw new DatabaseException("Invalid SQL: " . $sql);
                        }

                        break;
                    case "insert":
                        if (is_string($sql) && is_array($bind)) {
                            $result = $this->manager->query($sql, $bind)->insert();
                        } else {
                            throw new DatabaseException("Invalid SQL or bind parameters: " . $sql .", " . strval($bind));
                        }

                        break;
                    case "update":
                        if (is_string($sql) && is_array($bind)) {
                            $result = $this->manager->query($sql, $bind)->update();
                            throw new DatabaseException("Invalid SQL or bind parameters: " . $sql .", " . strval($bind));
                        }

                        break;
                    case "delete":
                        if (is_string($sql) && is_array($bind)) {
                            $result = $this->manager->query($sql, $bind)->delete();
                        } else {
                            throw new DatabaseException("Invalid SQL or bind parameters: " . $sql .", " . strval($bind));
                        }

                        break;
                }
            } elseif (preg_match('/^(?:(?:co(?:nnec|mmi)|disconnec)t|beginTransaction|rollback)$/', $method)) {
                $this->manager->{$method}();
            } else {
                if (array_key_exists(0, $arguments)) {
                    $bind = $arguments[0];
                }

                $modelMethod = debug_backtrace()[2]["function"];
                $queryKey = get_class($this) . "#" . $modelMethod;
                $query = $this->queryReader->getQuery($queryKey, $method);

                if ($query === null) {
                    throw new DatabaseException("SQL statement can't getting from xml file.");
                }

                $sql = $query["sql"];
                $method = $query["method"];

                if (is_array($bind)) {
                    $result = $this->manager->query($sql, $bind)->{$method}();
                } else {
                    $result = $this->manager->query($sql)->{$method}();
                }
            }

        } catch (DatabaseException $e) {
            $this->manager->rollback();
            $this->manager->disconnect();
            throw $e;
        }

        return $result;
    }
}
