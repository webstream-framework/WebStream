<?php
namespace WebStream;
/**
 * リクエストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/08/21
 */
class Request {
    /** GETパラメータ */
    private $get;
    /** POSTパラメータ */
    private $post;
    
    /**
     * コンストラクタ
     */
    public function __construct() {
        $this->get = safetyIn($_GET);
        $this->post = safetyIn($_POST);
    }
    
    /**
     * 安全な値に変換済みの全てのGETパラメータを返却する
     * @return Hash 安全なGETパラメータ
     */
    public function getGET() {
        return $this->get;
    }
    
    /**
     * 安全な値に変換済みの全てのPOSTパラメータを返却する
     * @return Hash 安全なPOSTパラメータ
     */
    public function getPOST() {
        return $this->post;
    }
    
    /**
     * ベースURLを取得する
     * @return String ベースURL
     */
    public function getBaseURL() {
        $script_name = $this->server("SCRIPT_NAME");
        $request_uri = $this->server("REQUEST_URI");

        // フロントコントローラが省略の場合
        $base_url = null;
        if (strpos($request_uri, $script_name) === 0) {
            $base_url = $script_name;
        }
        // フロントコントローラ指定の場合
        else if (strpos($request_uri, dirname($script_name)) === 0) {
            $base_url = rtrim(dirname($script_name), "/");
        }
        
        return $base_url;
    }
    
    /**
     * PATH情報を取得する
     * @return String PATH情報
     */
    public function getPathInfo() {
        $base_url = $this->getBaseURL();
        $request_uri = $this->server("REQUEST_URI");
        
        // GETパラメータ指定を除去する
        if (($pos = strpos($request_uri, "?")) !== false) {
            $request_uri = substr($request_uri, 0, $pos);
        }
        
        // PATH情報から取得する文字列を安全にする
        $path_info = safetyIn(substr($request_uri, strlen($base_url)));
        
        return $path_info;
    }
    
    /**
     * ヘッダを取得する
     * @param String ヘッダタイプ
     * @return String ヘッダ値
     */
    public function getHeader($type) {
        $headers = getallheaders();
        foreach ($headers as $key => $value) {
            if ($key === $type) {
                return $value;
            }
        }
    }
    
    /**
     * POSTかどうかチェックする
     * @return boolean POSTならtrue
     */
    public function isPost() {
        return $this->server("REQUEST_METHOD") === "POST" && (
            $this->getHeader("Content-Type") === "application/x-www-form-urlencoded" ||
            $this->getHeader("Content-Type") === "multipart/form-data"
        );
    }
    
    /**
     * GETかどうかチェックする
     * @return boolean GETならtrue
     */
    public function isGet() {
        return $this->server("REQUEST_METHOD") === "GET";
    }

    /**
     * SERVERパラメータ取得
     * @param String パラメータキー
     */
    private function server($key) {
        if (array_key_exists($key, $_SERVER)) {
            return safetyIn($_SERVER[$key]);
        }
        else {
            return null;
        }
    }
    
    /**
     * リファラを取得する
     * @return String リファラ
     */
    public function referer() {
        return $this->server("HTTP_REFERER");
    }
    
    /**
     * ユーザエージェントを取得する
     * @return String ユーザエージェント
     */
    public function userAgent() {
        return $this->server("HTTP_USER_AGENT");
    }
    
    /**
     * Basic認証のユーザIDを取得する
     * @return String Basic認証ユーザID
     */
    public function authUser() {
        return $this->server("PHP_AUTH_USER");
    }
    
    /**
     * Basic認証のパスワードを取得する
     * @return String Basic認証パスワード
     */
    public function authPassword() {
        return $this->server("PHP_AUTH_PW");
    }
    
    /**
     * GETパラメータ取得
     * @param String パラメータキー
     * @return String GETパラメータ
     */
    public function get($key) {
        if (array_key_exists($key, $this->get)) {
            return $this->get[$key];
        }
        else {
            return null;
        }
    }

    /**
     * POSTパラメータ取得
     * @param String パラメータキー
     * @return String POSTパラメータ
     */
    public function post($key) {
        if (array_key_exists($key, $this->post)) {
            return $this->post[$key];
        }
        else {
            return null;
        }
    }

    public function put() {}

    public function delete() {}
}