<?php
/**
 * リクエストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/08/21
 */
class Request {
    /**
     * ベースURLを取得する
     * @return ベースURL
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
     * @return PATH情報
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
     * GETパラメータ取得
     * @param String パラメータキー
     * @return String GETパラメータ
     */
    public function get($key) {
        if (array_key_exists($key, $_GET)) {
            return safetyIn($_GET[$key]);
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
        if (array_key_exists($key, $_POST)) {
            return safetyIn($_POST[$key]);
        }
        else {
            return null;
        }
    }

    public function put() {}

    public function delete() {}
}