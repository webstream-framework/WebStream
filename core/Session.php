<?php
namespace WebStream;
/**
 * セッションクラス
 * @author Ryuichi TANAKA.
 * @since 2010/08/24
 */
class Session {
    /** セッションアクセサ */
    private static $accessor = null;

    /**
     * インスタンス化は禁止
     */
    private function __construct() {
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    /**
     * セッションを開始する
     */
    public static function start() {
        if (!is_object(Session::$accessor)) {
            Session::$accessor = new Session();
        }
        return Session::$accessor;
    }

    /**
     * セッションを初期化する
     */
    public static function init() {
        $_SESSION = array();
        session_destroy();
    }
    
    /**
     * セッションIDを返却する
     * return String セッションID
     */
    public function id() {
        return session_id();
    }
    
    /**
     * セッションをセットする
     * @param String セッションキー
     * @param String セッション値
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * セッションを破棄する
     * @param String セッションキー
     */
    public function delete($key) {
        unset($_SESSION[$key]);
    }

    /**
     * セッションを取得する
     * @param String セッションキー
     * @return String セッション値
     */
    public function get($key) {
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
    }
}