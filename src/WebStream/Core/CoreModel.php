<?php
namespace WebStream\Core;

use WebStream\Annotation\Attributes\Filter;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Container\Container;
use WebStream\Database\DatabaseManager;
use WebStream\Database\Result;
use WebStream\DI\Injector;
use WebStream\Exception\Extend\DatabaseException;
use WebStream\Exception\Extend\MethodNotFoundException;
use WebStream\Util\CommonUtils;
use WebStream\Util\ApplicationUtils;
use Doctrine\DBAL\Connection;

/**
 * CoreModel
 * @author Ryuichi TANAKA.
 * @since 2012/09/01
 * @version 0.7
 */
class CoreModel implements CoreInterface, IAnnotatable
{
    use Injector, CommonUtils, ApplicationUtils;

    /**
     * @var Container コンテナ
     */
    private $container;

    /**
     * @var DatabaseManager データベースマネージャ
     */
    private $manager;

    /**
     * @var boolean オートコミットフラグ
     */
    private $isAutoCommit;

    /**
     * @var array<mixed> カスタムアノテーション
     */
    protected $annotation;

    /**
     * @var LoggerAdapter ロガー
     */
    protected $logger;

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->logger->debug("Model end.");
        $this->__clear();
    }

    /**
     * {@inheritdoc}
     * @Filter(type="initialize")
     */
    public function __initialize(Container $container)
    {
        $this->container = $container;
        $this->manager = new DatabaseManager($container);
        $this->isAutoCommit = true;
    }

    /**
    * {@inheritdoc}
     */
    public function __customAnnotation(array $annotation)
    {
        $this->annotation = $annotation;
    }

    /**
     * method missing
     * @param string メソッド名
     * @param array sql/bindパラメータ
     */
    final public function __call($method, $arguments)
    {
        // DBコネクションが取得できなければエラー
        $filepath = debug_backtrace()[0]["file"];

        if (!$this->manager->loadConnection($filepath)) {
            throw new MethodNotFoundException("Undefined method called: $method");
        }

        if ($this->manager->isConnected() === false) {
            $this->manager->connect();
        }

        $result = $this->__execute($method, $arguments, $filepath);

        if ($this->isAutoCommit) {
            if (is_int($result) && $result > 0) {
                $this->manager->commit();
            }
            $this->manager->disconnect();
        }

        return $result;
    }

    /**
     * DB処理を実行する
     * @param string メソッド名
     * @param array sql/bindパラメータ
     * @param string 現在実行中のクラスのファイルパス
     */
    final public function __execute($method, $arguments, $filepath)
    {
        $result = null;

        try {


            // TODO xmlのidにselect,update等が指定されると、メソッド実行の判定からのmethod_missingループになるバグ
            //
            if (preg_match('/^(?:select|(?:dele|upda)te|insert)$/', $method)) {
                $sql = $arguments[0];
                $bind = null;
                if (array_key_exists(1, $arguments)) {
                    $bind = $arguments[1];
                }

                if (is_string($sql)) {
                    if ($method !== 'select' && $this->isAutoCommit) {
                        $this->manager->beginTransaction(Connection::TRANSACTION_READ_COMMITTED);
                    }
                    if (is_array($bind)) {
                        $result = $this->manager->query($sql, $bind)->{$method}();
                    } else {
                        $result = $this->manager->query($sql)->{$method}();
                    }
                } else {
                    throw new DatabaseException("Invalid SQL or bind parameters: " . $sql .", " . strval($bind));
                }
            } else {
                $bind = null;
                if (array_key_exists(0, $arguments)) {
                    $bind = $arguments[0];
                }

                $trace = debug_backtrace();
                $modelMethod = null;
                for ($i = 0; $i < count($trace); $i++) {
                    if ($this->inArray($trace[$i]["function"], ["__call", "__execute"])) {
                        continue;
                    }

                    if ($trace[$i]["function"] !== null) {
                        $modelMethod = $trace[$i]["function"];
                        break;
                    }
                }

                $namespace = substr($this->getNamespace($filepath), 1);
                $queryKey = $namespace . "\\" . basename($filepath, ".php") . "#" . $modelMethod;
                $xpath = "//mapper[@namespace='$namespace']/*[@id='$method']";

                $queryInfo = $this->container->queryInfo;
                $queryList = $queryInfo($queryKey, $xpath);
                $query = array_shift($queryList);

                if ($query === null) {
                    throw new DatabaseException("SQL statement can't getting from xml file: " . $modelMethod);
                }

                $sql = $query["sql"];
                $method = $query["method"];
                $entityClassPath = $query["entity"];

                if ($entityClassPath !== null) {
                    if (!class_exists($entityClassPath)) {
                        throw new DatabaseException("Entity classpath is not found: " . $entityClassPath);
                    }

                    switch ($method) {
                        case "select":
                            if (is_string($sql)) {
                                if (is_array($bind)) {
                                    $result = $this->manager->query($sql, $bind)->select()->toEntity($entityClassPath);
                                } else {
                                    $result = $this->manager->query($sql)->select()->toEntity($entityClassPath);
                                }
                            } else {
                                $errorMessage = "Invalid SQL or bind parameters: " . $sql;
                                if (is_array($bind)) {
                                    $errorMessage .= ", " . strval($bind);
                                }

                                throw new DatabaseException($errorMessage);
                            }

                            break;
                        case "insert":
                        case "update":
                        case "delete":
                            // Not implement
                            throw new DatabaseException("Entity mapping is select only.");
                    }
                } else {
                    if (is_string($sql)) {
                        if ($method !== 'select' && $this->isAutoCommit) {
                            $this->manager->beginTransaction(Connection::TRANSACTION_READ_COMMITTED);
                        }
                        if (is_array($bind)) {
                            $result = $this->manager->query($sql, $bind)->{$method}();
                        } else {
                            $result = $this->manager->query($sql)->{$method}();
                        }
                    } else {
                        $errorMessage = "Invalid SQL or bind parameters: " . $sql;
                        if (is_array($bind)) {
                            $errorMessage .= ", " . strval($bind);
                        }

                        throw new DatabaseException($errorMessage);
                    }
                }
            }

            return $result;
        } catch (DatabaseException $e) {
            $this->manager->rollback();
            $this->manager->disconnect();
            throw $e;
        }
    }

    /**
     * トランザクション開始
     * @param int $isolationLevel トランザクション分離レベル
     */
    final public function beginTransaction(int $isolationLevel = Connection::TRANSACTION_READ_COMMITTED)
    {
        $filepath = debug_backtrace()[0]["file"];
        if (!$this->manager->loadConnection($filepath)) {
            throw new MethodNotFoundException("Undefined method called: $method");
        }

        if ($this->manager->isConnected() === false) {
            $this->manager->connect();
        }

        $this->manager->beginTransaction($isolationLevel);
        $this->isAutoCommit = false;
    }

    /**
     * コミットする
     */
    final public function commit()
    {
        if ($this->isAutoCommit === false) {
            $this->manager->commit();
        }
    }

    /**
     * ロールバックする
     */
    final public function rollback()
    {
        $this->manager->rollback();
    }
}
