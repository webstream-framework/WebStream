<?php
namespace WebStream\Http;

use WebStream\DI\Injector;
use WebStream\Exception\Extend\SessionTimeoutException;

/**
 * セッションクラス
 * @author Ryuichi TANAKA.
 * @since 2010/08/24
 * @version 0.7
 */
class Session
{
    use Injector;

    /** セッション名 */
    const SESSION_NAME = 'WSSESS';
    /** 初回起動チェッククッキー名 */
    const INITIAL_STARTED_COOKIE_NAME = 'WSSESS_STARTED';
    /** セッション有効期限を保存するクッキー名 */
    const SESSION_EXPIRE_COOKIE_NAME = 'WSSESS_LIFE';

    /**
     * コンストラクタ
     * @param int セッションの有効期限(秒)
     * @param string Cookieを有効にするパス
     * @param string Cookieを有効にするドメイン
     * @param boolean Secure属性を有効にする
     * @param boolean HttpOnly属性を有効にする
     */
    public function __construct($expire = null, $path = '/', $domain = "", $secure = false, $httpOnly = false)
    {
        $this->initialize($expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * デストラクタ
     */
    public function __destruct()
    {
        $this->logger->debug("Session is clear.");
    }

    /**
     * 初期設定
     * @param integer セッションの有効期限(秒)
     * @param string Cookieを有効にするパス
     * @param string Cookieを有効にするドメイン
     * @param boolean Secure属性を有効にする
     * @param boolean HttpOnly属性を有効にする
     */
    private function initialize($expire, $path, $domain, $secure, $httpOnly)
    {
        // 有効期限が設定されている場合のみ、Cookieに値をセットする
        if ($expire !== null) {
            session_set_cookie_params($expire, $path, $domain, $secure, $httpOnly);
        }

        // セッションIDの予測リスクを低減する
        // /dev/urandomまたは/dev/randomがある場合、
        // session.entropy_fileに設定し、session.entropy_lengthを32に設定する
        if (PHP_OS !== "WIN32" && PHP_OS !== "WINNT") {
            if (file_exists('/dev/urandom')) {
                ini_set('session.entropy_file', '/dev/urandom');
                ini_set('session.entropy_length', '32');
            } elseif (file_exists('/dev/random')) {
                ini_set('session.entropy_file', '/dev/random');
                ini_set('session.entropy_length', '32');
            }
            ini_set('session.save_path', '/tmp');
        } else {
            ini_set('session.save_path', 'C:\\tmp');
        }

        ini_set('session.hash_function', 'sha256');

        // RefererによるセッションID漏洩を防止する
        // セッションIDはCookieにのみ保存する
        ini_set('session.use_cookies', '1');
        ini_set('session.use_only_cookies', '1');
    }

    /**
     * セッションを開始する
     */
    public function start()
    {
        if (headers_sent()) {
            return;
        }

        // セッション名を設定
        session_name(self::SESSION_NAME);
        // セッションを開始
        session_start();

        // セッション固定化を防止
        // 注意：これをやるとセッション破棄される問題があるため外す。
        //session_regenerate_id(true);

        // 初回起動処理
        if ($this->isInitialStart()) {
            $this->createInitializeCookie();
        }

        // セッションタイムアウトかどうか。初回起動の場合は除く。
        if ($this->isSessionTimeout()) {
            $this->destroy();
            throw new SessionTimeoutException("Session timeout");
        }
    }

    /**
     * セッションを再始動する
     * @param integer セッションの有効期限(秒)
     * @param string Cookieを有効にするパス
     * @param string Cookieを有効にするドメイン
     * @param boolean Secure属性を有効にする
     * @param boolean HttpOnly属性を有効にする
     */
    public function restart($expire = null, $path = '/', $domain = '', $secure = false, $httpOnly = false)
    {
        if ($expire === null) {
            // 初期化する
            $this->createInitializeCookie();
        } else {
            // 再設定する
            setcookie(self::SESSION_EXPIRE_COOKIE_NAME, time(), time() + $expire, $path, $domain, $secure, $httpOnly);
            $_SESSION[self::SESSION_EXPIRE_COOKIE_NAME] = time() + $expire;
        }
    }

    /**
     * 初回起動時に生成するCookieを設定する
     */
    private function createInitializeCookie()
    {
        $_SESSION = [];
        setcookie(self::INITIAL_STARTED_COOKIE_NAME, time(), null, '/', null);
    }

    /**
     * セッションタイムアウト状態かどうか
     * WSSESS_LIFEクッキーが送られてこない場合、セッションタイムアウトとする
     * @return boolean セッションタイムアウトかどうか
     */
    private function isSessionTimeout()
    {
        return isset($_SESSION[self::SESSION_EXPIRE_COOKIE_NAME]) &&
               !isset($_COOKIE[self::SESSION_EXPIRE_COOKIE_NAME]);
    }

    /**
     * 初回起動時かどうか
     * WSSESS_STARTEDクッキーが削除されていた場合は強制的に初期化扱いする
     * @return boolean 初回起動時かどうか
     */
    private function isInitialStart()
    {
        return !isset($_COOKIE[self::INITIAL_STARTED_COOKIE_NAME]);
    }

    /**
     * セッションおよびCookieを破棄する
     */
    public function destroy()
    {
        // セッション変数を全て初期化
        $_SESSION = [];
        // Cookieを削除
        setcookie(session_name(), '', time() - 3600, '/');
        setcookie(self::INITIAL_STARTED_COOKIE_NAME, '', time() - 3600, '/');
        // セッションファイルを破棄
        session_destroy();
    }

    /**
     * セッションIDを返却する
     * @return string セッションID
     */
    public function id()
    {
        return session_id();
    }

    /**
     * セッションをセットする
     * @param string セッションキー
     * @param string セッション値
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * セッションを破棄する
     * @param string セッションキー
     */
    public function delete($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * セッションを取得する
     * @param string セッションキー
     * @return string セッション値
     */
    public function get($name)
    {
        if (isset($_SESSION) && array_key_exists($name, $_SESSION)) {
            return $_SESSION[$name];
        }
    }

    /**
     * セッションタイムアウトしたか返却する
     * @return boolean セッションタイムアウトしたかどうか
     */
    public function timeout()
    {
        return $this->isSessionTimeout();
    }
}
