<?php
namespace WebStream\Core;

use WebStream\Module\Container;
use WebStream\Module\Logger;
use WebStream\Module\PropertyProxy;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;
use WebStream\Database\DatabaseManager;
use WebStream\Database\Result;
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
    use PropertyProxy;

    /**
     * @var Container コンテナ
     */
    private $container;

    /**
     * @var DatabaseManager データベースマネージャ
     */
    private $manager;

    /**
     * @var array<AnnotationContainer> クエリアノテーションリスト
     */
    private $queryAnnotations;

    /**
     * @var boolean オートコミットフラグ
     */
    private $isAutoCommit;

    /**
     * @var array<mixed> カスタムアノテーション
     */
    protected $annotation;

    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        Logger::debug("Model start.");
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        Logger::debug("Model end.");
        $this->__clear();
    }

    /**
     * 初期化処理
     * @Inject
     * @Filter(type="initialize")
     */
    public function __initialize(Container $container)
    {
        if ($container->connectionContainerList === null) {
            Logger::warn("Can't use database in Model Layer.");

            return;
        }

        $this->queryAnnotations = $container->queryAnnotations;
        $this->manager = new DatabaseManager($container->connectionContainerList);
        $this->isAutoCommit = true;
    }

    /**
     * カスタムアノテーション情報を設定する
     * @param array<mixed> カスタムアノテーション情報
     */
    final public function __customAnnotation(array $annotation)
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
        $filepath = debug_backtrace()[1]["file"];

        // DBコネクションが取得できなければエラー
        if (!$this->manager->loadConnection($filepath)) {
            throw new MethodNotFoundException("Undefined method called: $method");
        }

        if ($this->manager->isConnected() === false) {
            $this->manager->connect();
        }

        $result = $this->__execute($method, $arguments);

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
     */
    final public function __execute($method, $arguments)
    {
        $result = null;

        try {
            if (preg_match('/^(?:select|(?:dele|upda)te|insert)$/', $method)) {
                $sql = $arguments[0];
                $bind = null;
                if (array_key_exists(1, $arguments)) {
                    $bind = $arguments[1];
                }

                if (is_string($sql)) {
                    if ($method !== 'select') {
                        $this->manager->beginTransaction();
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

                $modelMethod = debug_backtrace()[3]["function"];
                $queryKey = get_class($this) . "#" . $modelMethod;
                $queryId = $method;

                $refClass = new \ReflectionClass($this);
                $classpath = $refClass->getNamespaceName();

                $query = null;
                foreach ($this->queryAnnotations as $queryAnnotation) {
                    $queryFunctions = $queryAnnotation->get($queryKey);

                    if ($queryFunctions === null) {
                        continue;
                    }

                    foreach ($queryFunctions as $queryFunction) {
                        $xmlObjectList = $queryFunction->fetch();
                        foreach ($xmlObjectList as $xmlObject) {
                            if ($xmlObject !== null) {
                                $xmlElement = $xmlObject->xpath("//mapper[@namespace='$classpath']/*[@id='$queryId']");
                                if (!empty($xmlElement)) {
                                    $query = ["sql" => trim($xmlElement[0]), "method" => $xmlElement[0]->getName()];
                                    $entity = $xmlElement[0]->attributes()["entity"];
                                    $query["entity"] = $entity !== null ? $entity->__toString() : null;
                                }
                            }
                        }
                    }
                }

                if ($query === null) {
                    throw new DatabaseException("SQL statement can't getting from xml file.");
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
                        if ($method !== 'select') {
                            $this->manager->beginTransaction();
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
        } catch (DatabaseException $e) {
            $this->manager->rollback();
            $this->manager->disconnect();
            throw $e;
        }

        return $result;
    }

    /**
     * トランザクション開始
     */
    final public function beginTransaction()
    {
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
