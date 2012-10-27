<?php
namespace WebStream;
/**
 * セッションクラス
 * @author Ryuichi TANAKA.
 * @since 2010/08/24
 */
class Session {
    /** セッション名 */
    const SESSION_NAME = 'WSSESS';
    /** LAST_ACTIVITY */
    const LAST_ACTIVITY = '__LAST_ACTIVITY__';
    /** 初回起動チェッククッキー名 */
    const INITIAL_STARTED_COOKIE_NAME = 'WSSESS_STARTED';
    
    /** セッションアクセサ */
    private static $accessor = null;
    
    /**
     * コンストラクタ
     * @param Integer セッションの有効期限(秒)
     * @param String Cookieを有効にするパス
     * @param String Cookieを有効にするドメイン
     */
    private function __construct($expire, $path, $domain) {
        // セッションがすでに開始されている場合は終了
        if (isset($_SESSION)) return;
        // セッションIDの予測リスクを低減する
        // /dev/urandomまたは/dev/randomがある場合、
        // session.entropy_fileに設定し、session.entropy_lengthを32に設定する
        if (PHP_OS !== "WIN32" && PHP_OS !== "WINNT") {
            if (file_exists('/dev/urandom')) {
                ini_set('session.entropy_file', '/dev/urandom');
                ini_set('session.entropy_length', '32');
            }
            else if (file_exists('/dev/random')) {
                ini_set('session.entropy_file', '/dev/random');
                ini_set('session.entropy_length', '32');
            }
        }
        // RefererによるセッションID漏洩を防止する
        // セッションIDはCookieにのみ保存する
        ini_set('session.use_cookies', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.save_path', '/tmp');
        
        // 有効期限が設定されている場合のみ、Cookieに値をセットする
        if ($expire !== 0) {
            session_set_cookie_params($expire, $path, $domain);
        }
        // セッション名を設定
        session_name(self::SESSION_NAME);
        // セッションを開始
        session_start();
        // セッション固定化を防止
        session_regenerate_id(true);
        // 初回起動処理
        $this->createInitializeCookie();
        // 有効期限を設定
        if (!isset($_SESSION[self::LAST_ACTIVITY])) {
            // 初回起動でなく、LAST_ACTIVITYがない場合、セッションクッキーが送られてきて
            // いないのでセッションタイムアウト処理を実行
            if (!$this->isInitialStart()) {
                $this->destroy();
                throw new SessionTimeoutException("Session timeout");
            }
            // デフォルトは有効期限なし
            $_SESSION[self::LAST_ACTIVITY] = 0;
        }
        else if ($expire !== 0) {
            $_SESSION[self::LAST_ACTIVITY] = time() + $expire;
        }
    }

    /**
     * セッションを開始する
     * @param Integer セッションの有効期限(秒)
     * @param String Cookieを有効にするパス
     * @param String Cookieを有効にするドメイン
     */
    public static function start($expire = 0, $path = '/', $domain = '') {
        if (Session::$accessor === null) {
            Session::$accessor = new Session($expire, $path, $domain);
        }
        return Session::$accessor;
    }
    
    /**
     * セッションを再始動する
     * @param Integer セッションの有効期限(秒)
     * @param String Cookieを有効にするパス
     * @param String Cookieを有効にするドメイン
     */
    public static function restart($expire = 0, $path = '/', $domain = '') {
        session_regenerate_id(true);
        setcookie(session_name(), session_id(), time() + $expire, $path, $domain);
        $_SESSION[self::SESSION_NAME] = time() + $expire;
    }
    
    /**
     * 初回起動時に生成するCookieを設定する
     */
    private function createInitializeCookie() {
        if ($this->isInitialStart()) {
            $_SESSION = array();
            setcookie(self::INITIAL_STARTED_COOKIE_NAME, time(), null, '/', null);
        }
    }
    
    /**
     * 初回起動時かどうか
     * WSSESS_STARTEDクッキーが削除されていた場合は強制的に初期化扱いする
     * @return Boolean 初回起動時かどうか
     */
    private function isInitialStart() {
        return !isset($_COOKIE[self::INITIAL_STARTED_COOKIE_NAME]);
    }

    /**
     * セッションおよびCookieを破棄する
     */
    public function destroy() {
        // セッション変数を全て初期化
        $_SESSION = array();
        // Cookieを削除
        setcookie(session_name(), '', time() - 3600, '/');
        setcookie(self::INITIAL_STARTED_COOKIE_NAME, '', time() - 3600, '/');
        // セッションファイルを破棄
        session_destroy();
        // スタティック変数を初期化
        Session::$accessor = null;
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
    public function set($name, $value) {
        $_SESSION[$name] = $value;
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
    public function get($name) {
        if (isset($_SESSION) && array_key_exists($name, $_SESSION)) {
            return $_SESSION[$name];
        }
    }
    
    /**
     * セッションタイムアウトしたか返却する
     * @return Boolean セッションタイムアウトしたかどうか
     */
    public function timeout() {
        return !isset($_SESSION[self::LAST_ACTIVITY]);
    }
}