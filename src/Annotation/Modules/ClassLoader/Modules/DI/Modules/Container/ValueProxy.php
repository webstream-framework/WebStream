<?php
namespace WebStream\Container;

/**
 * ValueProxyクラス
 * @author Ryuichi TANAKA.
 * @since 2013/01/12
 * @version 0.4
 */
class ValueProxy
{
    /** コールバック関数 */
    private $callback;
    /** コンテキスト */
    private $context;
    /** キャッシュするかどうか */
    private $cached;
    /** コールバックの実行結果 */
    private $value;

    /**
     * コンストラクタ
     * @param  callable $callback コールバック関数
     * @param  array    $context  コンテキスト
     * @param  boolean  $cached   キャッシュするかどうか
     * @return void
     */
    public function __construct($callback, $context = [], $cached = true)
    {
        $this->callback = $callback;
        $this->cached   = $cached;
        $this->context  = $context;
    }

    /**
     * 評価する
     * @return mixed 実行結果
     */
    public function fetch()
    {
        if ($this->cached && isset($this->value)) {
            return $this->value;
        }
        $args = $this->context;
        // useで引数を指定した場合、call_user_func_arrayの$argsと
        // 引数の数が合わなくなり警告が出るが問題なく実行出来る
        $result = @call_user_func_array($this->callback, $args);
        if ($this->cached) {
            $this->value = $result;
        }

        return $result;
    }
}
