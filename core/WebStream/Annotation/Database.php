<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;

/**
 * Database
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 *
 * @Annotation
 * @Target("CLASS")
 */
class Database extends AbstractAnnotation
{
    /** database driver */
    private $driver;

    /** database config */
    private $config;

    /**
     * ＠Override
     */
    public function onInject()
    {
        $this->driver = $this->annotations[$this->DATABASE_ATTR_DRIVER];
        $this->config = $this->annotations[$this->DATABASE_ATTR_CONFIG];
        Logger::debug("Use database driver: " .$this->driver);
        Logger::debug("Database.");
    }

    /**
     * データベースドライバパスを返却する
     * @return string データベースドライバパス
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * データベース設定パスを返却する
     * @return string データベース設定パス
     */
    public function getConfig()
    {
        return $this->config;
    }
}
