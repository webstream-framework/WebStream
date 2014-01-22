<?php
namespace WebStream\Module;

use WebStream\Exception\IOException;

/**
 * FileSearchIterator
 * @author Ryuichi Tanaka
 * @since 2014/01/21
 * @version 0.4.1
 */
class FileSearchIterator implements \Iterator
{
    /** iterator */
    private $iterator;

    /**
     * constructor
     * @param string 検索パス
     * @param string 絞り込み条件正規表現
     */
    public function __construct($path, $regexp = null)
    {
        try {
            $this->iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path),
                \RecursiveIteratorIterator::LEAVES_ONLY,
                \RecursiveIteratorIterator::CATCH_GET_CHILD // for Permission deny
            );

            if ($regexp !== null) {
                $this->iterator = new \RegexIterator($this->iterator, $regexp, \RegexIterator::MATCH);
            }
        } catch (\InvalidArgumentException $e) {
            throw new IOException($e);
        }
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->iterator = null;
    }

    /**
     * Implements Iterator#current
     * 現在の要素を返却する
     * @return SplFileInfo SplFileInfoオブジェクト
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * Implements Iterator#key
     * 現在の要素のキーを返却する
     * @return string ファイルパス
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * Implements Iterator#next
     * 次の要素に進む
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * Implements Iterator#rewind
     * イテレータを先頭に巻き戻す
     */
    public function rewind()
    {
        $this->iterator->rewind();
    }

    /**
     * Implements Iterator#valid
     * 現在位置が有効かどうかを調べる
     * @return boolean 有効かどうか
     */
    public function valid()
    {
        return $this->iterator->valid();
    }
}
