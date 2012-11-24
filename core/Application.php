<?php
namespace WebStream;
/**
 * Applicationクラス
 * @author Ryuichi Tanaka
 * @since 2011/08/19
 */
class Application {
    /** アプリケーションファイルディレクトリ名 */
    private $app_dir = "app";
    /** Contollerインスタンス */
    private $controller;
    /** ルーティング解決後のパラメータ */
    private $route;
    /** バリデーション解決後のパラメータ */
    private $validate;
    /** リソースキャッシュパラメータ */
    private $cache = array();
    /** アプリケーションに注入されるアノテーション情報 */
    private $injection;
    
    /**
     * アプリケーション共通で使用するクラスを初期化する
     */
    public function __construct() {
        ob_start();
        ob_implicit_flush(false);
    }
    
    /**
     * アプリケーション終了時の処理
     */
    public function __destruct() {
        $buffer = ob_get_clean();
        $this->responseCache($buffer);
        echo $buffer;
    }
    
    /**
     * 内部で使用する定数を定義
     */
    private function init() {
        /** streamのバージョン定義 */
        define('STREAM_VERSION', '0.3.11');
        /** クラスパス */
        define('STREAM_CLASSPATH', '\\WebStream\\');
        /** プロジェクトディレクトリの絶対パスを定義 */
        define('STREAM_ROOT', Utility::getRoot());
        /** アプリケーションディレクトリ */
        define('STREAM_APP_DIR', $this->app_dir);
        /** ドキュメントルートからプロジェクトディレクトリへのパスを定義 */
        $request = new Request();
        define('STREAM_BASE_URI', $request->getBaseURL());
        define('STREAM_ROUTING_PATH', $request->getPathInfo());
        define('STREAM_QUERY_STRING', $request->getQueryString());
        /** publicディレクトリ */
        define('STREAM_VIEW_SHARED', "_shared");
        define('STREAM_VIEW_PUBLIC', "_public");
        define('STREAM_VIEW_CACHE', "_cache");
        /** レスポンスキャッシュID */
        define('STREAM_RESPONSE_CACHE_ID', 
               md5(STREAM_BASE_URI . STREAM_ROUTING_PATH . STREAM_QUERY_STRING));
        // CoreControllerのロード
        $this->controller = new CoreController();
    }
    
    /**
     * Controllerクラスをロード
     */
    private function loadController() {
        // Controllerクラスをインポート
        import(STREAM_APP_DIR . "/controllers/AppController");
        import(STREAM_APP_DIR . "/controllers/" . $this->controller());
    }
    
    /**
     * アプリケーションを起動する
     */
    public function run() {
        $this->init();
        $this->responseCache();
        try {
            // ルーティングを解決する
            $this->route = new Router();
            // Controllerクラスをロード
            $this->loadController();
            // アノテーション情報をセットする
            $this->injection = new Injection($this->controller(), $this->action());
            // セッションを開始
            Session::start();
            // ルーティングの解決に成功した場合、コントローラを呼び出す
            if ($this->controller() && $this->action()) {
                // バリデーションチェック
                $this->validate();
                // コントローラを起動
                $this->runContoller();
            }
            // 静的ファイルを呼び出す
            else if ($this->staticFile()) {
                $file_path = STREAM_ROOT . "/" . STREAM_APP_DIR . 
                    "/views/" . STREAM_VIEW_PUBLIC . $this->staticFile();
                $this->controller->__render_file($file_path);
            }
            // 存在しないURLにアクセスしたときは404
            else {
                throw new ResourceNotFoundException("Failed to resolve the routing");
            }
        }
        // CSRFエラーの場合は400
        catch (CsrfException $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->move(400);
            }
        }
        // セッションタイムアウトの場合は500
        catch (SessionTimeoutException $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->move(500);
            }
        }
        // 許可されないメソッドの場合は405
        catch (MethodNotAllowedException $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            $this->move(405);
        }
        // アクセス禁止の場合は403
        catch (ForbiddenAccessException $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            $this->move(403);
        }
        // リソース(URI)が見つからない場合は404
        catch (ResourceNotFoundException $e) {
            Logger::error($e->getMessage() . ": " . STREAM_ROUTING_PATH);
            $this->move(404);
        }
        // バリデーションエラーの場合は422
        catch (ValidateException $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e, $this->validate)) {
                $this->move(422);
            }
        }
        // それ以外のエラーは500
        catch (\Exception $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            $this->move(500);
        }
    }

    /**
     * コントローラを起動する
     */
    private function runContoller() {
        // Controllerクラスを起動
        $class = new \ReflectionClass(STREAM_CLASSPATH . $this->controller());
        $instance = $class->newInstance();
        // initialize
        $initialize = $class->getMethod("__initialize");
        $initialize->invoke($instance);
        // before_filter
        $this->before($class, $instance);
        // run annotation
        $this->runAnnotation($class, $instance);
        // action
        $action = $class->getMethod($this->action());
        $data = $action->invoke($instance, safetyIn($this->params()));
        // after_filter
        $this->after($class, $instance);
        // render template
        $this->render($class, $instance, $data);
    }
    
    /**
     * エラー処理のハンドリングチェック
     * @param Object エラーオブジェクト
     * @param Array エラー内容
     * @return Boolean ハンドリングするかどうか
     */
    private function handle($errorObj, $errorParams = array()) {
        $classPath = explode('\\', get_class($errorObj));
        $className = str_replace('Exception', '', end($classPath));
        $class = new \ReflectionClass(STREAM_CLASSPATH . $this->controller());
        $instance = $class->newInstance();
        $methodAnnotations = $this->injection->error();
        $isHandled = false;
        foreach ($methodAnnotations as $methodAnnotation) {
            // 大文字小文字を区別しない。CsrfでもCSRFでも通る。
            if (strcasecmp($methodAnnotation->value, $className) == 0) {
                $method = $class->getMethod($methodAnnotation->methodName);
                if (empty($errorParams)) {
                    $method->invoke($instance);
                }
                else {
                    $method->invoke($instance, $errorParams);
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
        $request = new Request();
        $ca = $this->route->controller() . "#" . $this->route->action();
        try {
            $validator->validateParameter($ca, $request->getGET(), "get");
            $validator->validateParameter($ca, $request->getPOST(), "post");
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
     * ステータスコードに合わせた画面に遷移する
     * @param String ステータスコード
     */
    private function move($statusCode) {
        $this->controller->__move($statusCode);
    }

    /**
     * コントローラ名を返却する
     * @return String コントローラ名
     */
    private function controller() {
        $controller = null;
        if ($this->route->controller() !== null) {
            // _[a-z]を[A-Z]に置換する
            $controller = preg_replace_callback('/_(?=[a-z])(.+?)/', function($matches) {
                return ucfirst($matches[1]);
            }, $this->route->controller());
            $controller = ucfirst($controller) . "Controller";
        }
        return $controller;
    }
    
    /**
     * アクション名を返却する
     * @return String アクション名
     */
    private function action() {
        $action = null;
        if ($this->route->action() !== null) {
            // _[a-z]を[A-Z]に置換する
            $action = preg_replace_callback('/_(?=[a-z])(.+?)/', function($matches) {
                return ucfirst($matches[1]);
            }, $this->route->action());
        }
        return $action;
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
            $render = $class->getMethod($info['method']);
            $render->invokeArgs($instance, $info['args']);
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
            "args" => $args
        );
    }

    /**
     * 有効なリクエストメソッドか検証する
     */
    private function requestMethod() {
        $request = new Request();
        $method = null;
        $methods = $this->injection->request();
        if ($request->isGet()) {
            $method = "GET";
        }
        else if ($request->isPost()) {
            $method = "POST";
        }
        if (!empty($methods) && !in_array($method, $methods)) {
            $errorMsg = $request->server("REQUEST_METHOD") . " method is not allowed";
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
            $request = new Request();
            if ($request->authUser() !==  $config["userid"] ||
                $request->authPassword() !== $config["password"]) {
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
     * レスポンスキャッシュを設定する
     * @param String キャッシュデータ
     */
    private function responseCache($data = null) {
        $cache = new Cache();
        $response = $cache->get(STREAM_RESPONSE_CACHE_ID);
        // キャッシュをセット
        if ($data) {
            if (array_key_exists('ttl', $this->cache) && !$response) {
                $cache->save(STREAM_RESPONSE_CACHE_ID, $data, $this->cache['ttl']);
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
     * パラメータを返却する
     * @return Array パラメータ
     */
    private function params() {
        return $this->route->params();
    }
    
    /**
     * 静的ファイルを返却する
     * @return Hash パラメータ
     */
    private function staticFile() {
        return $this->route->staticFile();
    }
}