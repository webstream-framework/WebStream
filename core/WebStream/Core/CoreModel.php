<?php
namespace WebStream\Core;

use WebStream\Module\Container;
use WebStream\Module\Logger;
use WebStream\Annotation\QueryReader;
use WebStream\Exception\Extend\DatabaseException;

/**
 * CoreModel
 * @author Ryuichi TANAKA.
 * @since 2012/09/01
 * @version 0.4
 */
class CoreModel implements CoreInterface
{
    /** manager */
    private $connection;

    /** container */
    private $container;

    /** query reqder */
    private $queryReader;

    /**
     * Override
     */
    public function __construct(Container $container)
    {
        Logger::debug("Model start.");
        $this->container = $container;
        $this->manager = $container->manager;
        $this->manager->connect();
        $this->manager->beginTransaction();
        $this->queryReader = new QueryReader();
    }

    /**
     * Override
     */
    public function __destruct()
    {
        Logger::debug("Model end.");
    }

    /**
     * method missing
     * @param string メソッド名
     * @param array sql/bindパラメータ
     */
    public function __call($method, $arguments)
    {
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

                $refClass = new \ReflectionClass($this);
                $methodName = $this->container->router->routingParams()["action"];

                $this->queryReader->setId($method);
                $this->queryReader->read($refClass, $methodName, $this->container);
                $sql = $this->queryReader->getQuery();

                if ($sql === null) {
                    throw new DatabaseException("SQL statement can't getting from xml file.");
                }

                if (is_array($bind)) {
                    $result = $this->manager->query($sql, $bind)->select();
                } else {
                    $result = $this->manager->query($sql)->select();
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
