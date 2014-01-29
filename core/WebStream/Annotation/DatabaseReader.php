<?php
namespace WebStream\Annotation;

use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use WebStream\Module\Logger;
use WebStream\Database\DatabaseManager;
use WebStream\Exception\DatabaseException;

/**
 * DatabaseReader
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
class DatabaseReader extends AnnotationReader
{
    /**
     * @Override
     */
    public function readAnnotation($refClass, $method, $container)
    {
        $reader = new DoctrineAnnotationReader();
        $class = $reader->getClassAnnotation($refClass, "\WebStream\Annotation\Database");
        if ($class === null || $class->getDriver() === null) {
            Logger::warn("Can't connect database because database driver is undefined in model.");
            return;
        }

        $driverClassPath = $class->getDriver();
        if (!class_exists($driverClassPath)) {
            throw new DatabaseException("Database driver is undefined：" . $driverClassPath);
        }

        $configPath = STREAM_APP_ROOT . "/" . $class->getConfig();
        $configRealPath = realpath($configPath);
        if (!file_exists($configRealPath)) {
            throw new DatabaseException("Database config file is not found: " . $configPath);
        }

        $config = parse_ini_file($configRealPath);
        $driver = new $driverClassPath();
        $container->manager = new DatabaseManager($driver, $config);
        $constructor = $refClass->getConstructor();

        if ($constructor !== null) {
            $constructor->invokeArgs($this->instance, [$container]);
        }
    }
}
