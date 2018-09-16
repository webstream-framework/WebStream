<?php
namespace WebStream\Annotation\Attributes;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Base\IClass;
use WebStream\Container\Container;
use WebStream\Exception\Extend\DatabaseException;
use WebStream\IO\File;

/**
 * Database
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.7
 *
 * @Annotation
 * @Target("CLASS")
 */
class Database extends Annotation implements IClass, IRead
{
    /**
     * @var array<string> 注入アノテーション情報
     */
    private $injectAnnotation;

    /**
     * @var array<string> 読み込みアノテーション情報
     */
    private $readAnnotation;

    /**
     * {@inheritdoc}
     */
    public function onInject(array $injectAnnotation)
    {
        $this->injectAnnotation = $injectAnnotation;
        $this->readAnnotation = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAnnotationInfo(): array
    {
        return $this->readAnnotation;
    }

    /**
     * {@inheritdoc}
     */
    public function onClassInject(IAnnotatable $instance, \ReflectionClass $class, Container $container)
    {
        $driver = $this->injectAnnotation['driver'];
        $config = $this->injectAnnotation['config'];

        if (!class_exists($driver)) {
            throw new DatabaseException("Database driver is undefined：" . $driver);
        }

        $file = new File($container->rootPath . '/' . $config);
        $this->readAnnotation['filepath'] = $class->getFileName();
        $this->readAnnotation['configPath'] = $file->getAbsoluteFilePath();
        $this->readAnnotation['driverClassPath'] = $driver;
    }
}
