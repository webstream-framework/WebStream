<?php
namespace WebStream\Core;

use WebStream\Module\Container;
use WebStream\Module\Logger;
use WebStream\Annotation\QueryReader;
use WebStream\Exception\DatabaseException;

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

    /**
     * Override
     */
    public function __construct(Container $container)
    {
        Logger::debug("Model start.");
        $this->container = $container;
        $this->manager = $container->manager;
        $this->manager->connect();
    }

    /**
     * Override
     */
    public function __destruct()
    {
        Logger::debug("Model end.");
    }

    public function __call($method, $arguments)
    {
        $sql = null;
        $bind = null;
        $result = null;

        if (count($arguments) === 1) {
            $bind = $arguments[0];
            $refClass = new \ReflectionClass($this);
            $methodName = $this->container->router->routingParams()["action"];

            $reader = new QueryReader();
            $reader->setId($method);
            $reader->read($refClass, $methodName, $this->container);
            $sql = $reader->getQuery();

            if ($sql === null) {
                throw new DatabaseException("SQL statement can't getting from xml file.");
            }

            if (is_array($bind)) {
                $result = $this->manager->query($sql, $bind)->select();
            } else {
                $result = $this->manager->query($sql)->select();
            }
        } else {
            $sql = $arguments[0];
            $bind = $arguments[1];
            if (preg_match('/(?:select|(?:dele|upda)te|insert)/', $method)) {
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
                        } else {
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
            } elseif (preg_match('/(?:create|drop)/', $method)) {
                // $sql = $arguments[0];
                // return $this->db->{$method}($sql);
            }

        }

        return $result;
    }
}
