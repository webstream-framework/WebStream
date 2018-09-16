<?php
namespace WebStream\Http;

use WebStream\Util\Security;
use WebStream\Container\Container;
use WebStream\DI\Injector;

/**
 * Request
 * @author Ryuichi TANAKA.
 * @since 2013/11/12
 * @version 0.7
 */
class Request
{
    use Injector;

    /**
     * @var Container リクエストコンテナ
     */
    private $container;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->container = new Container(false);
        $this->createRequestContainer();
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->logger->debug("Request is clear.");
    }

    /**
     * リクエストコンテナを返却する
     * @return Container リクエストコンテナ
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * リクエストコンテナを作成する
     */
    private function createRequestContainer()
    {
        $this->container->referer = $this->server("HTTP_REFERER");
        $this->container->userAgent = $this->server("HTTP_USER_AGENT");
        $this->container->requestMethod = $this->server("REQUEST_METHOD");
        $this->container->authUser = $this->server("PHP_AUTH_USER");
        $this->container->authPassword = $this->server("PHP_AUTH_PW");
        $this->container->queryString = $this->server("QUERY_STRING");
        $this->container->requestUri = $this->server("REQUEST_URI");
        $this->container->httpHost = $this->server("HTTP_HOST");

        $scriptName = $this->server("SCRIPT_NAME");
        if (strpos($this->container->requestUri, $scriptName) === 0) {
            // フロントコントローラが省略の場合
            $this->container->baseUrl = $scriptName;
        } elseif (strpos($this->container->requestUri, dirname($scriptName)) === 0) {
            // フロントコントローラ指定の場合
            $this->container->baseUrl = rtrim(dirname($scriptName), "/");
        }

        $requestUri = $this->container->requestUri;
        // GETパラメータ指定を除去する
        if (($pos = strpos($requestUri, "?")) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        // PATH情報から取得する文字列を安全にする
        $this->container->pathInfo = Security::safetyIn(substr($requestUri, strlen($this->container->baseUrl)));

        if (function_exists('getallheaders')) {
            $headers = [];
            foreach (getallheaders() as $key => $value) {
                $headers[$key] = Security::safetyIn($value);
            }
            $this->container->header = $headers;
        }

        $scriptName = $this->server("SCRIPT_NAME");
        $requestUri = $this->server("REQUEST_URI");
        if (strpos($requestUri, $scriptName) === 0) {
            // フロントコントローラが省略の場合
            $this->container->baseUri = $scriptName;
        } elseif (strpos($requestUri, dirname($scriptName)) === 0) {
            // フロントコントローラ指定の場合
            $this->container->baseUri = rtrim(dirname($scriptName), "/");
        }

        $this->container->get = $this->container->post = $this->container->put = $this->container->delete = [];
        switch ($this->container->requestMethod) {
            case 'GET':
                $this->container->get = Security::safetyIn($_GET);
                break;
            case 'POST':
                $this->container->post = Security::safetyIn($_POST);
                break;
            case 'PUT':
                parse_str(file_get_contents('php://input'), $putdata);
                $this->container->put = Security::safetyIn($putdata);
                break;
            case 'DELETE':
                // not implements
                break;
            default:
                break;
        }
    }

    /**
     * SERVERパラメータ取得
     * @param string パラメータキー
     */
    private function server($key)
    {
        if (array_key_exists($key, $_SERVER)) {
            return Security::safetyIn($_SERVER[$key]);
        } else {
            return null;
        }
    }
}
