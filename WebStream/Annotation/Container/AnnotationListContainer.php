<?php
namespace WebStream\Annotation\Container;

use WebStream\Container\ValueProxy;
use WebStream\Exception\Extend\CollectionException;

/**
 * AnnotationListContainer
 * @author Ryuichi TANAKA.
 * @since 2014/05/21
 * @version 0.4
 */
class AnnotationListContainer extends AnnotationContainer implements \Iterator, \SeekableIterator, \ArrayAccess, \Countable
{
    /** index */
    private $index;

    /** カーソル位置 */
    private $position;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->index = 0;
        $this->position = 0;
    }

    /**
     * 値を登録する
     * @param mixed $value 値
     */
    public function push($value)
    {
        $this->values[$this->index++] = $value;
    }

    /**
     * 遅延評価の値を登録する
     * @param callable $callback クロージャ
     * @param array    $context  クロージャの引数リスト
     */
    public function pushAsLazy($callback, $context = [])
    {
        $this->values[$this->index++] = new ValueProxy($callback, $context, true);
    }

    /**
     * Implements Countable#count
     * @return integer 結果件数
     */
    public function count()
    {
        return count($this->values);
    }

    /**
     * Implements SeekableIterator#seek
     * カーソル位置を移動する
     * @param mixed オフセット
     */
    public function seek($offset)
    {
        if (!array_key_exists($offset, $this->values)) {
            throw new \OutOfBoundsException("Current cursor is out of range: " . $offset);
        }

        return $this->values[$offset];
    }

    /**
     * Implements Iterator#current
     * 現在の要素を返却する
     * @return array<string> 列データ
     */
    public function current()
    {
        return $this->values[$this->position];
    }

    /**
     * Implements Iterator#key
     * 現在の要素のキーを返却する
     * @return integer キー
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Implements Iterator#next
     * 次の要素に進む
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * Implements Iterator#rewind
     * イテレータを先頭に巻き戻す
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Implements Iterator#valid
     * 現在位置が有効かどうかを調べる
     * @return boolean 有効かどうか
     */
    public function valid()
    {
        return array_key_exists($this->position, $this->values);
    }

    /**
     * Implements ArrayAccess#offsetExists
     * オフセットの位置に値が存在するかどうか返却する
     * @return boolean 値が存在するかどうか
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->values);
    }

    /**
     * Implements ArrayAccess#offsetGet
     * オフセットの位置の値を返却する
     * @return mixed 値
     */
    public function offsetGet($offset)
    {
        return array_key_exists($offset, $this->values) ? $this->values[$offset] : null;
    }

    /**
     * Implements ArrayAccess#offsetSet
     * オフセットの位置に値を設定する
     * @param mixed オフセット
     * @param mixed 値
     */
    public function offsetSet($offset, $value)
    {
        throw new CollectionException("AnnotationListContainer are read only.");
    }

    /**
     * Implements ArrayAccess#offsetUnSet
     * オフセットの設定を解除する
     * @param mixed オフセット
     */
    public function offsetUnSet($offset)
    {
        throw new CollectionException("AnnotationListContainer is read only.");
    }
}
