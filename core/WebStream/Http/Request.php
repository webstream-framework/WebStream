<?php
namespace WebStream\Http;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Type;
use WebStream\Module\Security;

class Request
{
    /**
     * @Autowired
     * @Type("\WebStream\Http\Method\Get")
     */
    private $get;

    /** 各メソッドパラメータを保持 */
    private $methodMap;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->methodmap = [
            'get' => $this->get->params()
        ];
    }

    /**
     * ベースURLを取得する
     * @return String ベースURL
     */
    public function getBaseURL()
    {
        $script_name = $this->server("SCRIPT_NAME");
        $request_uri = $this->server("REQUEST_URI");

        $base_url = null;
        if (strpos($request_uri, $script_name) === 0) {
            // フロントコントローラが省略の場合
            $base_url = $script_name;
        } elseif (strpos($request_uri, dirname($script_name)) === 0) {
            // フロントコントローラ指定の場合
            $base_url = rtrim(dirname($script_name), "/");
        }

        return $base_url;
    }

    /**
     * PATH情報を取得する
     * @return String PATH情報
     */
    public function getPathInfo()
    {
        $base_url = $this->getBaseURL();
        $request_uri = $this->server("REQUEST_URI");

        // GETパラメータ指定を除去する
        if (($pos = strpos($request_uri, "?")) !== false) {
            $request_uri = substr($request_uri, 0, $pos);
        }

        // PATH情報から取得する文字列を安全にする
        $path_info = Security::safetyIn(substr($request_uri, strlen($base_url)));

        return $path_info;
    }

    /**
     * クエリストリングを返却する
     * @return String クエリストリング
     */
    public function getQueryString()
    {
        return $this->server("QUERY_STRING");
    }

    /**
     * ヘッダを取得する
     * @param String ヘッダタイプ
     * @return String ヘッダ値
     */
    public function getHeader($type)
    {
        $headers = getallheaders();
        foreach ($headers as $key => $value) {
            if ($key === $type) {
                return $value;
            }
        }
    }

    /**
     * SERVERパラメータ取得
     * @param String パラメータキー
     */
    public function server($key)
    {
        if (array_key_exists($key, $_SERVER)) {
            return Security::safetyIn($_SERVER[$key]);
        } else {
            return null;
        }
    }

    /**
     * リファラを取得する
     * @return String リファラ
     */
    public function referer()
    {
        return $this->server("HTTP_REFERER");
    }

    /**
     * リクエストメソッドを取得する
     * @return String リクエストメソッド
     */
    public function requestMethod()
    {
        return $this->server("REQUEST_METHOD");
    }

    /**
     * ユーザエージェントを取得する
     * @return String ユーザエージェント
     */
    public function userAgent()
    {
        return $this->server("HTTP_USER_AGENT");
    }

    /**
     * Basic認証のユーザIDを取得する
     * @return String Basic認証ユーザID
     */
    public function authUser()
    {
        return $this->server("PHP_AUTH_USER");
    }

    /**
     * Basic認証のパスワードを取得する
     * @return String Basic認証パスワード
     */
    public function authPassword()
    {
        return $this->server("PHP_AUTH_PW");
    }

    /**
     * GETパラメータ取得
     * @param String パラメータキー
     * @return String|Hash GETパラメータ
     */
    public function get($key = null)
    {
        if ($key === null) {
            return $this->methodmap['get'];
        }
        return array_key_exists($key, $this->methodmap['get']) ? $this->methodmap['get'][$key] : null;
    }

    public function post()
    {
    }

    public function put()
    {
    }

    public function delete()
    {
    }
}
