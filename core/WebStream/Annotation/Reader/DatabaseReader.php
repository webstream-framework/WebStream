<?php
namespace WebStream\Annotation\Reader;

use WebStream\Module\Container;
use WebStream\Annotation\Container\AnnotationListContainer;
use WebStream\Exception\Extend\DatabaseException;
use WebStream\Exception\Extend\AnnotationException;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * DatabaseReader
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
class DatabaseReader extends AbstractAnnotationReader
{
    /**
     * @var AnnotationListContainer DB接続情報コンテナリスト
     */
    private $connectionItemContainerList;

    /**
     * {@inheritdoc}
     */
    public function onRead()
    {
        $this->annotation = $this->reader->getAnnotation("WebStream\Annotation\Database");
        $this->connectionItemContainerList = new AnnotationListContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->annotation === null) {
            return;
        }

        try {
            $refClass = $this->reader->getReflectionClass();

            while ($refClass !== false) {
                $classpath = $refClass->getName();
                // アノテーションが取得できなかった場合はエラーにはせずDB接続なしのModelとして扱う
                if (array_key_exists($classpath, $this->annotation)) {
                    $databaseContainer = $this->annotation[$classpath];
                    $driverClassPath = $databaseContainer->driver;

                    if (!class_exists($driverClassPath)) {
                        throw new DatabaseException("Database driver is undefined：" . $driverClassPath);
                    }

                    $configPath = STREAM_APP_ROOT . "/" . $databaseContainer->config;
                    $configRealPath = realpath($configPath);
                    if (!file_exists($configRealPath)) {
                        throw new DatabaseException("Database config file is not found: " . $configPath);
                    }

                    // ここではパスを読み取る以上のこと(権限)はしない
                    // 処理自体はDatabaseManager,ConnectionManagerに委譲
                    $container = new Container();
                    $container->filepath = $refClass->getFileName();
                    $container->configPath = $configRealPath;
                    $container->driverClassPath = $driverClassPath;
                    $this->connectionItemContainerList->push($container);
                }

                $refClass = $refClass->getParentClass();
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e);
        }
    }

    /**
     * DB接続情報コンテナリストを返却する
     * @return AnnotationListContainer DB接続情報コンテナリスト
     */
    public function getConnectionItemContainerList()
    {
        return $this->connectionItemContainerList;
    }
}
