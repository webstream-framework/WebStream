<?php
namespace WebStream\Util;

/**
 * Singleton
 * @author Ryuichi Tanaka
 * @since 2015/03/06
 * @version 0.7
 */
trait Singleton
{
    /**
     * @var インスタンス
     */
    private static $instance;

    /**
     * constructor
     */
    public function __construct()
    {
    }

    public function __destruct()
    {
        $this->__clear();
    }

    /**
     * インスタンスを返却する
     * @return object シングルトンインスタンス
     */
    public static function getInstance()
    {
        if (!is_object(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function __clear()
    {
        static::$instance = null;
    }

    /**
     * cloneは許可しない
     */
    public function __clone()
    {
        throw new \RuntimeException("Can't clone this instance.");
    }
}
