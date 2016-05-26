<?php
namespace WebStream\Log;

/**
 * LoggerCache
 * @author Ryuichi Tanaka
 * @since 2016/05/23
 * @version 0.7
 */
class LoggerCache
{
    /**
     * @var string キャッシュ接頭辞
     */
    private $prefix;

    /**
     * @var int キャッシュインデックス
     */
    private $index;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->prefix = md5(get_class($this));
        $this->index = 0;
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        apcu_clear_cache();
    }

    /**
     * キャッシュに追加
     * @param string $content キャッシュデータ
     */
    public function add(string $content)
    {
        apcu_add($this->prefix . $this->index++, $content);
    }

    /**
     * キャッシュを返却する
     * @return array<string> キャッシュデータ
     */
    public function get()
    {
        $list = [];
        for ($i = 0; $i < $this->index; $i++) {
            $list[] = apcu_fetch($this->prefix . $i);
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
