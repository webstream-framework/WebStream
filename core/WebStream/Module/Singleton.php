<?php
namespace WebStream\Module;

/**
 * Singleton
 * @author Ryuichi Tanaka
 * @since 2015/03/06
 * @version 0.4
 */
trait Singleton
{
    /**
     * @var インスタンス
     */
    private static $__obj;

    /**
     * constructor
     */
    private function __construct()
    {
    }

    /**
     * インスタンスを返却する
     * @return object シングルトンインスタンス
     */
    public static function getInstance()
    {
        if (!is_object(self::$__obj)) {
            self::$__obj = new self();
        }

        return self::$__obj;
    }

    /**
     * cloneは許可しない
     */
    public function __clone()
    {
        throw new \RuntimeException("Can't clone this instance.");
    }
}
