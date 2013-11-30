<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreController;
use WebStream\Module\Container;
use WebStream\Module\Cache;
use WebStream\Module\Utility;
use WebStream\Exception\RouterException;
use WebStream\Exception\ResourceNotFoundException;
use WebStream\Exception\ClassNotFoundException;
use WebStream\Exception\AnnotationException;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;
use WebStream\Module\Logger;
use WebStream\Annotation\ExceptionHandlerReader;

/**
 * Resolver
 * @author Ryuichi TANAKA.
 * @since 2012/12/22
 * @version 0.4
 */
class Resolver
{
    use Utility;

    /** ルーティングオブジェクト */
    private $router;
    /** リクエストオブジェクト */
    private $request;
    /** レスポンスオブジェクト */
    private $response;
    /** セッションオブジェクト */
    private $session;
    /** DIコンテナ */
    private $container;

    /**
     * コンストラクタ
     * @param Object DIコンテナ
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->request   = $container->request;
        $this->response  = $container->response;
        $this->session   = $container->session;
        $this->router    = $container->router;
    }

    /**
     * Resolverを起動する
     */
    public function run()
    {
        // ルータインスタンスをセットする必要がある
        if (!$this->router instanceof Router) {
            throw new RouterException("Required router instance to start the Controller");
        }

        // ルーティング解決を実行
        $this->router->resolve();
        // セッションスタート
        $this->session->start();
        // バッファリング開始
        $this->response->start();

        if ($this->router->controller() !== null && $this->router->action() !== null) {
            $this->runController();
        } elseif ($this->router->staticFile() !== null) {
            $this->readFile();
        } else {
            $errorMsg = "Failed to resolve the routing: " . $this->request->server("REQUEST_URI");
            throw new ResourceNotFoundException($errorMsg);
        }

        $this->response->end();
    }

    /**
     * Controllerを起動する
     */
    private function runController()
    {
        // ファイルパスを取得
        $filepathList = $this->fileSearch($this->router->controller());
        $filepath = array_shift($filepathList);
        // 名前空間を取得
        $namespace = $this->getNamespace($filepath);
        // クラスパス生成
        $classpath = $namespace . '\\' . $this->router->controller();

        // バリデーションチェック
        $validator = $this->container->validator;
        $validator->check();

        // テンプレートキャッシュチェック
        $pageName = preg_replace("/Controller/", "", $this->router->controller());
        $cacheFile = STREAM_CACHE_PREFIX . $this->camel2snake($pageName) . "-" . $this->camel2snake($this->router->action());
        $cache = new Cache(STREAM_ROOT . "/" . STREAM_APP_DIR . "/views/" . STREAM_VIEW_CACHE);
        $data = $cache->get($cacheFile);

        if ($data !== null) {
            echo $data;

            return;
        }

        try {
            // Controller起動
            $refClass = new \ReflectionClass($classpath);
            $instance = $refClass->newInstance($this->container);
            $method = $refClass->getMethod("__callInitialize");
            $method->invokeArgs($instance, [$refClass, $this->router->action(), $this->router->params(), $this->container]);
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException($e);
        }
    }

    /**
     * ファイルを読み込む
     */
    private function readFile()
    {
        $controller = new CoreController($this->container);
        $controller->__callStaticFile($this->router->staticFile());
    }

    /**
     * 指定したステータスコードのページに遷移する
     * @param Integer ステータスコード
     */
    public function move($statusCode)
    {
        $this->response->move($statusCode);
    }

    /**
     * エラー処理のハンドリングチェック
     * @param object エラーオブジェクト
     * @param array エラー内容
     * @return boolean ハンドリングするかどうか
     */
    public function handle(\Exception $e)
    {
        $filepathList = $this->fileSearch($this->router->controller());
        $filepath = array_shift($filepathList);
        $namespace = $this->getNamespace($filepath);
        $classpath = $namespace . '\\' . $this->router->controller();
        $ca = $classpath;
        $validator = $this->container->validator;
        $errorInfo = [
            "class" => $classpath,
            "method" => $this->router->action()
        ];

        try {
            // Controller起動
            $refClass = new \ReflectionClass($classpath);
            // @ExceptionHandlerを起動
            $reader = new ExceptionHandlerReader();
            $reader->setHandledException($e);
            $reader->read($refClass, null, $this->container);
            $handleMethods = $reader->getHandleMethods();

            if (count($handleMethods) === 0) {
                return false;
            }

            for ($i = 0; $i < count($handleMethods); $i++) {
                $handleMethod = $handleMethods[$i];
                $ca = $classpath . "#" . $handleMethod;
                $instance = $refClass->newInstance($this->container);
                $method = $refClass->getMethod($handleMethod);
                $method->invokeArgs($instance, [$errorInfo]);
                Logger::debug("Execution of handling is success: " . $ca);
            }

            return true;
        } catch (DoctrineAnnotationException $e) {
            Logger::error("Error occued in handled method: " . $ca);
            throw new AnnotationException($e);
        } catch (\ReflectionException $e) {
            Logger::error("Error occued in handled method: " . $ca);
            throw new ApplicationException($e);
        }

        return false;
    }
}
