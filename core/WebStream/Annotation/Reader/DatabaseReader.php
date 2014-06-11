<?php
namespace WebStream\Annotation\Reader;

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
    /** データベース設定 */
    private $config;

    /** データベースドライバ */
    private $driver;

    /**
     * {@inheritdoc}
     */
    public function onRead()
    {
        $this->annotation = $this->reader->getAnnotation("WebStream\Annotation\Database");
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
                $key = $refClass->getName();
                // アノテーションが取得できなかった場合はエラーにはせずDB接続なしのModelとして扱う
                if (array_key_exists($key, $this->annotation)) {
                    $container = $this->annotation[$key];
                    $driverClassPath = $container->driver;

                    if (!class_exists($driverClassPath)) {
                        throw new DatabaseException("Database driver is undefined：" . $driverClassPath);
                    }

                    $configPath = STREAM_APP_ROOT . "/" . $container->config;
                    $configRealPath = realpath($configPath);
                    if (!file_exists($configRealPath)) {
                        throw new DatabaseException("Database config file is not found: " . $configPath);
                    }

                    // TODO Modelごとに異なるDBMSが指定された場合の処理
                    // 仕様上ありえるので対応したい

                    $this->config = parse_ini_file($configRealPath);
                    $this->driver = new $driverClassPath();
                }

                $refClass = $refClass->getParentClass();
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        }
    }

    /**
     * データベース設定を返却する
     * @return array<string> データベース設定
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * データベースドライバを返却する
     * @return array<string> データベース設定
     */
    public function getDriver()
    {
        return $this->driver;
    }
}
