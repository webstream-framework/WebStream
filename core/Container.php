<?php
namespace WebStream;
/**
 * Containerクラス
 * @author Ryuichi TANAKA.
 * @since 2013/01/12
 */
class Container {
    /** パラメータMap */
    protected $values = array();

    /**
     * magic method of set
     * @param string $key キー
     * @param mixed $value 値
     * @return void
     */
    public function __set($key, $value) {
        $this->set($key, $value);
    }

    /**
     * magic method of get
     * @param string $key キー
     * @return mixed 値
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * 未定義のメソッドを処理
     * @param string $name メソッド名
     * @param array $arguments 引数リスト
     * @return void
     */
    public function __call($name, $arguments) {
        if (is_callable($arguments[0])) {
            array_unshift($arguments, $name);
            call_user_func_array(array($this, 'registerAsLazy'), $arguments);
        }
        else {
            $this->set($name, $arguments[0]);
        }
    }

    /**
     * キーの値を設定する
     * @param string $name メソッド名
     * @param array $arguments 引数リスト
     * @return void
     */
    public function set($key, $value) {
        $this->values[$key] = $value;
    }

    /**
     * 格納した値を取得
     * @param string $key キー
     * @throws InvalidArgumentException
     * @return mixed 格納値
     */
    public function get($key) {
        if (!isset($this->values[$key])) {
            throw new \InvalidArgumentException("The value of the specified key does not exist: $key");
        }
        if ($this->values[$key] instanceof ValueProxy) {
            return $this->values[$key]->fetch();
        }
        else {
            return $this->values[$key];
        }
    }

    /**
     * 格納された値を削除する
     * @param string $key キー
     * @return void
     */
    public function remove($key) {
        unset($this->values[$key]);
    }

    /**
     * 値を登録する
     * @param string $key キー
     * @param string $value 値
     * @return void
     */
    public function register($key, $value) {
        $this->set($key, $value);
    }

    /**
     * 即時実行した値を登録する
     * @param string $key キー
     * @param callable $callback クロージャ
     * @param array $context クロージャの引数リスト
     * @return void
     */
    public function registerAsDynamic($key, $callback, $context = array()) {
        $args = func_get_args();
        $key = array_shift($args);
        $callback = array_shift($args);
        $context = $args;
        $valueObject = new ValueProxy($callback, $context, true);
        $this->values[$key] = $valueObject->fetch();
    }

    /**
     * 遅延評価の値を登録する
     * @param string $key キー
     * @param callable $callback クロージャ
     * @param array $context クロージャの引数リスト
     * @return void
     */
    public function registerAsLazy($key, $callback, $context = array()) {
        $args = func_get_args();
        $key = array_shift($args);
        $callback = array_shift($args);
        $context = $args;
        $this->values[$key] = new ValueProxy($callback, $context, true);
    }

    /**
     * 遅延評価の値を登録する
     * 繰り返し実行されたときにキャッシュしない
     * @param string $key キー
     * @param callable $callback クロージャ
     * @param array $context クロージャの引数リスト
     * @return void
     */
    public function registerAsLazyUnCached($key, $callback, $context = array()) {
        $args = func_get_args();
        $key = array_shift($args);
        $callback = array_shift($args);
        $context = $args;
        $this->values[$key] = new ValueProxy($callback, $context, false);
    }
}
