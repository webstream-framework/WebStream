<?php
namespace WebStream\Http;

use WebStream\Module\Security;
use WebStream\Module\Logger;
use WebStream\Http\Method\Get;
use WebStream\Http\Method\Post;
use WebStream\Http\Method\Put;

/**
 * Request
 * @author Ryuichi TANAKA.
 * @since 2013/11/12
 * @version 0.4.1
 */
class Request
{
    /**
     * @var Get GETパラメータ
     */
    private $get;

    /**
     * @var Post POSTパラメータ
     */
    private $post;

    /**
     * @var Put PUTパラメータ
     */
    private $put;

    // private $delete;
    // private $head;
    // private $options;
    // private $trace;

    /** ドキュメントルートパス */
    private $documentRoot;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->get = new Get();
        $this->post = new Post();
        $this->put = new Put();
    }

    /**
     * デストラクタ
     */
    public function __destruct()
    {
        Logger::debug("Request is clear.");
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
     * REQUEST_URI情報を取得する
     * @return string REQUEST_URI情報
     */
    public function getRequestUri()
    {
        return $this->server("REQUEST_URI");
    }

    /**
     * PATH情報を取得する
     * @return string PATH情報
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
        $pathInfo = Security::safetyIn(substr($request_uri, strlen($base_url)));

        return $pathInfo;
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
     * @param string パラメータキー
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
     * GETかどうかチェックする
     * @return boolean GETならtrue
     */
    public function isGet()
    {
        return $this->requestMethod() === "GET";
    }

    /**
     * POSTかどうかチェックする
     * @return boolean POSTならtrue
     */
    public function isPost()
    {
        return $this->requestMethod() === "POST" && (
            $this->getHeader("Content-Type") === "application/x-www-form-urlencoded" ||
            $this->getHeader("Content-Type") === "multipart/form-data"
        );
    }

    /**
     * PUTかどうかチェックする
     * @return boolean PUTならtrue
     */
    public function isPut()
    {
        return $this->requestMethod() === "PUT";
    }

    /**
     * DELETEかどうかチェックする
     * @return boolean DELETEならtrue
     */
    public function isDelete()
    {
        // not implementation
        return false;
    }

    /**
     * GETパラメータ取得
     * @param string パラメータキー
     * @return string|array<string> GETパラメータ
     */
    public function get($key = null)
    {
        $params = $this->get->params();

        return $key === null ? $params : (array_key_exists($key, $params) ? $params[$key] : null);
    }

    /**
     * POSTパラメータ取得
     * @param string パラメータキー
     * @return string|array<string> POSTパラメータ
     */
    public function post($key = null)
    {
        $params = $this->post->params();

        return $key === null ? $params : (array_key_exists($key, $params) ? $params[$key] : null);
    }

    /**
     * PUTパラメータ取得
     * PUTを使用してレスポンスを返す場合、
     * リソース新規作成：201
     * リソース更新：200または204
     * を返却しなければならない
     * @param string パラメータキー
     * @return string|array<string> PUTパラメータ
     */
    public function put($key = null)
    {
        $params = $this->put->params();

        return $key === null ? $params : (array_key_exists($key, $params) ? $params[$key] : null);
    }

    /**
     * DELETEパラメータ取得
     * DELETEを使用してレスポンスを返す場合、
     * 200、202、204のいずれかを返却しなければならない
     */
    public function delete()
    {
        // not implementation
        return null;
    }
}
