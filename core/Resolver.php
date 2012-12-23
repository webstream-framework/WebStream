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

    /**
     * コンストラクタ
     * @param Object レスポンスオブジェクト
     */    
    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
        // FIXME ここはControllerオブジェクトそのものを渡したほうがよさそう
        //$this->injection = new Injection($this->router->controller(), $this->router->action());
    }

    /**
     * コンストラクタ
     * @param Object ルータオブジェクト
     */   
    public function setRouter(Router $router) {
        $this->router = $router;
    }

    /**
     * Controllerを起動する
     */ 
    public function run() {
        $this->load();
        $this->injection = new Injection($this->router->controller(), $this->router->action());
        // ルータインスタンスをセットする必要がある
        if ($this->router instanceof WebStream\Router) {
            throw new RouterException("Required router instance to start the Controller");
        }
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
        $this->render($class, $instance, $data);
    }

    private function readFile() {
    	// タイムアウトのチェック
        Session::start();
        $filePath = STREAM_ROOT . "/" . STREAM_APP_DIR . 
            "/views/" . STREAM_VIEW_PUBLIC . $this->router->staticFile();
        $this->instance->__render_file($filePath);
    }

    private function move($statusCode) {
    	$this->instance->__move($statusCode);
    }

    /**
     * クラスをロードする
     */ 
    private function load() {
    	// Controllerクラスをロードする
    	if ($this->router->controller() !== null) {
            import(STREAM_APP_DIR . "/controllers/AppController");
            import(STREAM_APP_DIR . "/controllers/" . $this->router->controller());
            $this->class = new \ReflectionClass(STREAM_CLASSPATH . $this->router->controller());
            // newInstanceだとcall_user_func()で参照渡しができないバグがあるためcall_user_func_array()
            // を内部的に呼び出しているnewInstanceArgsを使用する
            $this->instance = 
                $this->class->newInstanceArgs(array(&$this->request, &$this->response));
    	}
    	// 静的ファイルまたはルーティング解決失敗の場合
    	else {
    		$this->instance = new CoreController($this->request, $this->response);
    	}
    }


    public function isSuccessRouting() {
    	return $this->router->controller() && $this->router->action();
    }

    public function existFile() {
    	return !!$this->router->staticFile();
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
        $ca = $this->router->controller() . "#" . $this->router->action();
        try {
            $validator->validateParameter($ca, $this->request->getGET(), "get");
            $validator->validateParameter($ca, $this->request->getPOST(), "post");
        }
        catch (ValidateException $e) {
            $this->validate = array(
                "class" => $this->controller(),
                "method" => $this->action(),
                "error" => $validator->getError()
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
    private function render($class, $instance, $data) {
        $info = $this->renderInfo($class, $instance, $data);
        if (isset($info)) {
            $render = $class->getMethod('__templates');
            $render->invoke($instance, $info['list']);
            $render = $class->getMethod('__renderMethods');
            $render->invoke($instance, $info['methods']);
            $render = $class->getMethod($info['method']);
            $render->invokeArgs($instance, $info['args']);
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
        $renderInfo = $this->injection->render();
        $renderMethods = $renderInfo['methods'];
        $templates = $renderInfo['templates'];
        $renderMethod = $renderInfo['method'];

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
        $responseFormat = $this->injection->format();
        if (empty($templates)) {
            switch ($responseFormat) {
            case 'json':
                $renderMethod = "__render_json";
                $args = array($params);
                break;
            case 'jsonp':
                $renderMethod = "__render_jsonp";
                $args = array(
                    $params,
                    $params[$this->injection->callback()]
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
        $methods = $this->injection->request();
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
        $configPath = $this->injection->basicAuth();
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
        $ttl = $this->injection->cache();
        if ($ttl !== null) {
            if (!preg_match('/^\d+$/', $ttl)) {
                $errorMsg = "@Cache value must be positive integer. Found value: $methodAnnotation->value";
                throw new AnnotationException($errorMsg);
            }
            $this->cache['ttl'] = $ttl;
        }
    }

    /**
     * CSRF対策を有効にする
     * @param Object リフレクションクラスオブジェクト
     * @param Object リフレクションクラスインスタンスオブジェクト
     */
    private function csrf($class, $instance) {
        if ($this->injection->security() === "CSRF") {
            $method = $class->getMethod('__enableCsrf');
            $method->invoke($instance);
        }
    }
}