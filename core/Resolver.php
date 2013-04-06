<?php
namespace WebStream;
/**
 * リゾルバクラス
 * @author Ryuichi TANAKA.
 * @since 2012/12/22
 */
class Resolver {
    /** ルーティングオブジェクト */
    private $router;
    /** リクエストオブジェクト */
    private $request;
    /** レスポンスオブジェクト */
    private $response;
    /** Contollerクラスオブジェクト */
    private $class;
    /** Contollerインスタンスオブジェクト */
    private $instance;
    /** アプリケーションに注入されるアノテーション情報 */
    private $injection;
    /** バリデーション解決後のパラメータ */
    private $validate;
    /** レスポンスキャッシュのTTL */
    private $responseCacheTTL;
    /** DIコンテナ */
    private $container;

    /**
     * コンストラクタ
     * @param Object DIコンテナ
     */    
    public function __construct(Container $container) {
        $this->container = $container;
        $this->request   = $container->request;
        $this->response  = $container->response;
        $this->router    = $container->router;
        $this->class     = new \ReflectionClass(STREAM_CLASSPATH . 'CoreController');
        $this->instance  = $this->class->newInstance($container);
    }

    /**
     * Resolverを起動する
     */ 
    public function run() {
        // ルータインスタンスをセットする必要がある
        if ($this->router instanceof WebStream\Router) {
            throw new RouterException("Required router instance to start the Controller");
        }
        // ルーティング解決を実行
        $this->router->resolve();
        $this->controllerLoader();
        $this->injectionLoader();

        if ($this->isSuccessRouting()) {
            $this->runController();
        }
        else if ($this->existFile()) {
            $this->readFile();
        }
        // 存在しないURLにアクセスしたときは404
        else {
            throw new ResourceNotFoundException("Failed to resolve the routing");
        }
    }

    /**
     * Controllerを起動する
     */ 
    private function runController() {
        // タイムアウトのチェック
        Session::start();
        // バリデーションチェック
        $this->validate();
        // コントローラを起動
        $class = $this->class;
        $instance = $this->instance;
        // initialize
        $initialize = $class->getMethod("__initialize");
        $initialize->invoke($instance);
        // before_filter
        $this->before($class, $instance);
        // run annotation
        $this->runAnnotation($class, $instance);
        // action
        $action = $class->getMethod($this->router->action());
        $data = $action->invoke($instance, safetyIn($this->router->params()));
        // after_filter
        $this->after($class, $instance);
        // render template
        $this->renderView($class, $instance, $data);
    }

    /**
     * ファイルを読み込む
     */ 
    private function readFile() {
        // タイムアウトのチェック
        Session::start();
        $filePath = STREAM_ROOT . "/" . STREAM_APP_DIR . 
            "/views/" . STREAM_VIEW_PUBLIC . $this->router->staticFile();
        $render = $this->class->getMethod('__callView');
        $render->invoke($this->instance, '__file', array($filePath));
    }

    /**
     * 指定したステータスコードのページに遷移する
     * @param Integer ステータスコード
     */ 
    public function move($statusCode) {
        $this->response->move($statusCode);
    }

    /**
     * Controllerクラスをロードする
     */
    private function controllerLoader() {
        // Controllerクラスをロードする
        if ($this->router->controller() !== null) {
            import(STREAM_APP_DIR . "/controllers/AppController");
            import(STREAM_APP_DIR . "/controllers/" . $this->router->controller());
            $this->class = new \ReflectionClass(STREAM_CLASSPATH . $this->router->controller());
            $this->instance = $this->class->newInstance($this->container);
        }
    }

    /**
     * Injectionクラスをロードする
     */
    private function injectionLoader() {
        if ($this->class instanceof \ReflectionClass) {
            $this->injection = new Injection($this->class);
        }
    }

    /**
     * レスポンスオブジェクト処理を実行する
     * @param String キャッシュデータ
     */
    public function responseCache($data = null) {
        $cache = new Cache();
        $response = $cache->get(STREAM_RESPONSE_CACHE_ID);
        // キャッシュをセット
        if ($data) {
            if ($this->responseCacheTTL !== null && !$response) {
                $cache->save(STREAM_RESPONSE_CACHE_ID, $data, $this->responseCacheTTL);
                Logger::info("Response cache rendered.");
            }
        }
        // キャッシュをロード
        else {
            if ($response) {
                echo $response;
                Logger::info("Response cache loaded.");
                exit;
            }
        }
    }

    /**
     * ルーティングが成功したかどうか
     * @return Boolean ルーティング解決結果
     */
    public function isSuccessRouting() {
        return $this->router->controller() && $this->router->action();
    }

    /**
     * ファイルが存在するかどうか
     * @return Boolean ファイルが存在するかどうか
     */
    public function existFile() {
        return !!$this->router->staticFile();
    }

    /**
     * バリデーションエラーパラメータを返却する
     * @return Hash バリデーションパラメータ
     */
    public function getValidateErrors() {
        return $this->validate;
    }

    /**
     * エラー処理のハンドリングチェック
     * @param Object エラーオブジェクト
     * @param Array エラー内容
     * @return Boolean ハンドリングするかどうか
     */
    public function handle($errorObj, $errorParams) {
        $isHandled = false;
        $classPath = explode('\\', get_class($errorObj));
        $className = str_replace('Exception', '', end($classPath));
        $methodAnnotations = $this->injection->error();
        foreach ($methodAnnotations as $methodAnnotation) {
            // 大文字小文字を区別しない。CsrfでもCSRFでも通る。
            if (strcasecmp($methodAnnotation->value, $className) == 0) {
                $method = $this->class->getMethod($methodAnnotation->methodName);
                if (empty($errorParams)) {
                    $method->invoke($this->instance);
                }
                else {
                    $method->invoke($this->instance, $errorParams);
                }
                $isHandled = true;
            }
        }
        return $isHandled;
    }

    /**
     * バリデーションを実行する
     */
    private function validate() {
        $validator = new Validator();
        // GET, POSTパラメータ両方を検査する
        $route = $this->router->routingParams();
        $ca = $route['controller'] . "#" . $route['action'];
        try {
            $validator->validateParameter($ca, $this->request->getGET(), "get");
            $validator->validateParameter($ca, $this->request->getPOST(), "post");
        }
        catch (ValidateException $e) {
            $this->validate = array(
                "class"  => $this->router->controller(),
                "method" => $this->router->action(),
                "error"  => $validator->getError()
            );
            throw $e;
        }
    }

    /**
     * Before Filterを実行する
     * @param Object リフレクションクラスオブジェクト
     * @param Object リフレクションクラスインスタンスオブジェクト
     */
    private function before($class, $instance) {
        $this->filter($class, $instance, "Before");
    }
    
    /**
     * After Filterを実行する
     * @param Object リフレクションクラスオブジェクト
     * @param Object リフレクションクラスインスタンスオブジェクト
     */
    private function after($class, $instance) {
        $this->filter($class, $instance, "After");
    }

    /**
     * Filter処理を実行する
     * @param Object リフレクションクラスオブジェクト
     * @param Object リフレクションクラスインスタンスオブジェクト
     * @param String Filter名
     */
    private function filter($class, $instance, $filterName) {
        $filterObjectList = $this->injection->filter();
        foreach ($filterObjectList as $filterObject) {
            // 複数のメソッドに対してアノテーションを定義可能とする
            if ($filterObject->value === $filterName) {
                // クラス名が一致しない場合、親クラスを辿り一致するまで走査する
                // それでも一致しなければメソッドを持っていないと判断する
                $_class = $class;
                do {
                    if ($_class->getName() === $filterObject->className &&
                        $_class->hasMethod($filterObject->methodName)) {
                        $method = $_class->getMethod($filterObject->methodName);
                        $method->invoke($instance);
                    }
                }
                while ($_class = $_class->getParentClass());
            }
        }
    }

    /**
     * アノテーションを実行
     * @param Object リフレクションクラスオブジェクト
     * @param Object リフレクションクラスインスタンスオブジェクト
     */
    private function runAnnotation($class, $instance) {
        // request method
        $this->requestMethod();
        // basic auth
        $this->basicAuth();
        // cache
        $this->cache();
        // csrf processing
        $this->csrf($class, $instance);
    }

    /**
     * Viewテンプレートを描画する
     * @param Object リフレクションクラスオブジェクト
     * @param Object リフレクションクラスインスタンスオブジェクト
     * @param Hash 描画データ
     */
    private function renderView($class, $instance, $data) {
        $info = $this->renderInfo($class, $instance, $data);
        if (isset($info)) {
            $render = $class->getMethod('__callView');
            $render->invoke($instance, '__templates', array($info['list']));
            $render->invoke($instance, '__renderMethods', array($info['methods']));
            $render->invoke($instance, $info['method'], $info['args']);
        }
        else {
            Logger::info("Template is not rendered.");
        }
    }

    /**
     * レンダリング情報を取得する
     * @param Object リフレクションクラスオブジェクト
     * @param Object リフレクションクラスインスタンスオブジェクト
     * @param Hash レンダリングデータ
     * @return Hash レンダリング情報
     */
    private function renderInfo($class, $instance, $params) {
        $renderInfo    = $this->injection->render($this->router->action());
        $renderMethods = $renderInfo['methods'];
        $templates     = $renderInfo['templates'];
        $renderMethod  = $renderInfo['method'];

        // Viewでレンダリングするようのハッシュを作成する
        // key: xxx.tmplに記述した@{yyy}と一致する名前
        // value: @{yyy}にひもづく実際のテンプレートファイル名
        $templateList = array();
        for ($i = 0; $i < count($templates); $i++) {
            $args = $templates[$i];
            // @Render/@Layoutの引数が0または1つの場合、テンプレートリストに登録しない
            if (count($args) === 0 || count($args) === 1) continue;
            $templateList[$args[1]] = $args[0];
        }
        // 最初に描画するテンプレートを指定
        $responseFormat = $this->injection->format($this->router->action());
        if (empty($templates)) {
            switch ($responseFormat) {
            case 'json':
                $renderMethod = "__json";
                $args = array($params);
                break;
            case 'jsonp':
                $renderMethod = "__jsonp";
                $args = array(
                    $params,
                    $params[$this->injection->callback($this->router->action())]
                );
                break;
            default:
                return;
            }
        }
        else {
            // html, xml, atom, rss
            $args = array($templates[0][0], $params, $responseFormat);
        }

        return array(
            "list" => $templateList,
            "method" => $renderMethod,
            "methods" => $renderMethods,
            "args" => $args
        );
    }

    /**
     * 有効なリクエストメソッドか検証する
     */
    private function requestMethod() {
        $method = null;
        $methods = $this->injection->request($this->router->action());
        if ($this->request->isGet()) {
            $method = "GET";
        }
        else if ($this->request->isPost()) {
            $method = "POST";
        }
        if (!empty($methods) && !in_array($method, $methods)) {
            $errorMsg = $this->request->server("REQUEST_METHOD") . " method is not allowed";
            throw new MethodNotAllowedException($errorMsg);
        }
    }

    /**
     * 基本認証を実行する
     */
    private function basicAuth() {
        $configPath = $this->injection->basicAuth($this->router->action());
        if ($configPath !== null) {
            $config = Utility::parseConfig($configPath);
            if ($config === null) {
                $errorMsg = "Properties file specified by @BasicAuth annotation is not found: $methodAnnotation->value";
                throw new AnnotationException($errorMsg);
            }
            if ($this->request->authUser() !==  $config["userid"] ||
                $this->request->authPassword() !== $config["password"]) {
                $this->move(401);
            }
        }
    }

    /**
     * キャッシュ情報を設定する
     */
    private function cache() {
        $ttl = $this->injection->cache($this->router->action());
        if ($ttl !== null) {
            if (!preg_match('/^\d+$/', $ttl)) {
                $errorMsg = "@Cache value must be positive integer. Found value: $methodAnnotation->value";
                throw new AnnotationException($errorMsg);
            }
            $this->responseCacheTTL = $ttl;
        }
    }

    /**
     * CSRF対策を有効にする
     * @param Object リフレクションクラスオブジェクト
     * @param Object リフレクションクラスインスタンスオブジェクト
     */
    private function csrf($class, $instance) {
        if ($this->injection->security($this->router->action()) === "CSRF") {
            $render = $this->class->getMethod('__callView');
            $render->invoke($this->instance, '__enableCsrf');
        }
    }
}