<?php
namespace WebStream\Annotation;

use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use WebStream\Module\Logger;
use WebStream\Database\DatabaseManager;
use WebStream\Exception\ClassNotFoundException;
use WebStream\Exception\ResourceNotFoundException;

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
            throw new ClassNotFoundException("Database driver is undefined：" . $driverClassPath);
        }

        $configPath = realpath(STREAM_ROOT . "/" . STREAM_APP_DIR . "/../" . $class->getConfig());
        if (!file_exists($configPath)) {
            throw new ResourceNotFoundException("Database config file is not found: " . $configPath);
        }

        $config = parse_ini_file($configPath);
        $driver = new $driverClassPath();
        $container->manager = new DatabaseManager($driver, $config);
        $constructor = $refClass->getConstructor();

        if ($constructor !== null) {
            $constructor->invokeArgs($this->instance, [$container]);
        }
    }
}
