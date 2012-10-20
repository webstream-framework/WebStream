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
     * コンストラクタ
     * @param Integer セッションの有効期限(秒)
     * @param String Cookieを有効にするパス
     * @param String Cookieを有効にするドメイン
     */
    private function __construct($expire, $path, $domain) {
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
        // 有効期限が設定されている場合のみ、Cookieに値をセットする
        if ($expire !== 0) {
            session_set_cookie_params($expire, $path, $domain);
            // 有効期限を設定
            if (!isset($_SESSION['__LAST_ACTIVITY__'])) {
                $_SESSION['__LAST_ACTIVITY__'] = time();
            }
        }
        session_name("WSSESS");
        session_start();
        // セッション固定化を防止
        session_regenerate_id(true);
    }

    /**
     * セッションを開始する
     * @param Integer セッションの有効期限(秒)
     * @param String Cookieを有効にするパス
     * @param String Cookieを有効にするドメイン
     */
    public static function start($expire = 0, $path = '/', $domain = '') {
        if (!is_object(Session::$accessor)) {
            Session::$accessor = new Session($expire, $path, $domain);
        }
        return Session::$accessor;
    }

    /**
     * セッションおよびCookieを破棄する
     */
    public function destroy() {
        // セッション変数を全て初期化
        $_SESSION = array();
        // Cookieを削除
        if (isset($_COOKIE[session_name()])) {
            // Cookieの有効期限を1時間前にセットし期限切れを起こす
            setcookie(session_name(), '', time() - 3600, '/');
        }
        // セッションIDを破棄
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
    public function set($name, $value, $expire = 0, $path = '', $domain = '') {
        $sessionData = array(
            'value' => $value,
            'expire' => $expire === 0 ? 60*60*24*365 : time() + intval($expire),
            'path' => $path,
            'domain' => $domain
        );
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
        if (array_key_exists($name, $_SESSION)) {
            return $_SESSION[$name];
        }
    }
    
    /**
     * セッションクッキーを設定する
     * @param Integer セッションの有効期限(秒)
     * @param String Cookieを有効にするパス
     * @param String Cookieを有効にするドメイン
     */
    public function cookie($expire = 0, $path = '/', $domain = '') {
        setcookie(session_name(), session_id(), time() + $expire, $path, $domain);
    }
    
    /**
     * セッションタイムアウトしたか返却する
     * @return Boolean セッションタイムアウトしたかどうか
     */
    public function timeout() {
        $cookies = session_get_cookie_params();
        $lifetime = $cookies['lifetime'];
        return isset($_SESSION['__LAST_ACTIVITY__']) && 
            $_SESSION['__LAST_ACTIVITY__'] + $lifetime < time();
    }
}