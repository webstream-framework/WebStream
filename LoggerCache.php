<?php
namespace WebStream\Log;

use WebStream\Cache\Driver\ICache;

/**
 * LoggerCache
 * @author Ryuichi Tanaka
 * @since 2016/05/23
 * @version 0.7
 */
class LoggerCache
{
    /**
     * @var ICache キャッシュドライバ
     */
    private $driver;

    /**
     * @var string キャッシュキー
     */
    private $key;

    /**
     * @var int キャッシュインデックス
     */
    private $index;

    /**
     * constructor
     */
    public function __construct(ICache $driver)
    {
        $this->driver = $driver;
        $this->key = md5(get_class($this));
        $this->index = 0;
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->driver->clear();
    }

    /**
     * キャッシュに追加
     * @param string $content キャッシュデータ
     */
    public function add(string $content)
    {
        $this->driver->add($this->key . $this->index++, $content);
    }

    /**
     * キャッシュを返却する
     * @return array<string> キャッシュデータ
     */
    public function get()
    {
        $list = [];
        for ($i = 0; $i < $this->index; $i++) {
            $list[] = $this->driver->get($this->key . $i);
        }

        return $list;
    }

    /**
     * キャッシュデータ数を返却する
     * @return int キャッシュデータ数
     */
    public function length()
    {
        return $this->index;
    }
}
