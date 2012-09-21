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
    /** ルーティング解決後のパラメータ */
    private $route;
    /** バリデーション解決後のパラメータ */
    private $validate;
    
    /**
     * アプリケーション共通で使用するクラスを初期化する
     */
    public function __construct() {
        /** streamのバージョン定義 */
        define('STREAM_VERSION', '0.3.2');
    }
    
    /**
     * 内部で使用する定数を定義
     */
    private function init() {
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
        /** publicディレクトリ */
        define('STREAM_VIEW_SHARED', "_shared");
        define('STREAM_VIEW_PUBLIC', "_public");
        define('STREAM_VIEW_CACHE', "_cache");
    }
    
    /**
     * アプリケーションを起動する
     */
    public function run() {
        $this->init();
        try {
            // ルーティングを解決する
            $this->route = new Router();
            // ルーティングの解決に成功した場合、コントローラを呼び出す
            if ($this->controller() && $this->action()) {
                $this->runContoller();
            }
            // 静的ファイルを呼び出す
            else if ($this->staticFile()) {
                $controller = new CoreController();
                $file_path = STREAM_ROOT . "/" . STREAM_APP_DIR . 
                    "/views/" . STREAM_VIEW_PUBLIC . $this->staticFile();
                $controller->render_file($file_path);
            }
            // 存在しないURLにアクセスしたときは404
            else {
                throw new ResourceNotFoundException("Failed to resolve the routing");
            }
        }
        // CSRFエラーの場合は400
        catch (CsrfException $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            $this->error(400);
        }
        // アクセス禁止の場合は403
        catch (ForbiddenAccessException $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            $this->error(403);
        }
        // リソース(URI)が見つからない場合は404
        catch (ResourceNotFoundException $e) {
            Logger::error($e->getMessage() . ": " . STREAM_ROUTING_PATH);
            $this->error(404);
        }
        // バリデーションエラーの場合は422
        catch (ValidatorException $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            $this->error(422);
        }
        // それ以外のエラーは500
        catch (\Exception $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            $this->error(500);
        }
    }

    /**
     * コントローラを起動する
     */
    private function runContoller() {
        // Controllerクラスをインポート
        import(STREAM_APP_DIR . "/controllers/AppController");
        import(STREAM_APP_DIR . "/controllers/" . $this->controller());
        // Controllerクラスを起動
        $class = new \ReflectionClass(STREAM_CLASSPATH . $this->controller());
        $instance = $class->newInstance();
        // initialize
        $initialize = $class->getMethod("initialize");
        $initialize->invoke($instance);
        // before_filter
        $this->before($class, $instance);
        // validate
        $this->validate($class, $instance);
        // action
        $action = $class->getMethod($this->action());
        $action->invoke($instance, safetyIn($this->params()));
        // after_filter
        $this->after($class, $instance);
    }
    
    /**
     * エラー画面を表示する
     * @param int ステータスコード
     */
    private function error($status_code) {
        $controller = new CoreController();
        $controller->move($status_code);
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
     * Filter処理を実行する
     * @param Object リフレクションクラスオブジェクト
     * @param Object リフレクションクラスインスタンスオブジェクト
     * @param String Filter名
     */
    private function filter($class, $instance, $filterName) {
        $annotation = new Annotation(STREAM_CLASSPATH . $this->controller());
        $methodAnnotations = $annotation->methods("@Filter");
        foreach ($methodAnnotations as $methodAnnotation) {
            // @Filter($filterName)を抽出
            // 複数のメソッドに対してアノテーションを定義可能とする
            if ($methodAnnotation->value === $filterName) {
                if ($class->hasMethod($methodAnnotation->methodName)) {
                    $hasHandlingMethod = true;
                    $method = $class->getMethod($methodAnnotation->methodName);
                    $method->invoke($instance);
                }
            }
        }
    }
    
    /**
     * バリデーションを実行する
     * @param Object リフレクションクラスオブジェクト
     * @param Object リフレクションクラスインスタンスオブジェクト
     */
    private function validate($class, $instance) {
        $validator = new Validator();
        // GET, POSTパラメータ両方を検査する
        $request = new Request();
        $ca = $this->route->controller() . "#" . $this->route->action();
        try {
            $validator->validateParameter($ca, $request->getGET(), "get");
            $validator->validateParameter($ca, $request->getPOST(), "post");
        }
        catch (ValidatorException $e) {
            // Controllerクラスでバリデーションエラーを補足するメソッドが
            // オーバーライドされていれば例外は出さずにそのメソッドへエラー内容を移譲する
            // オーバーライドされていなければ例外を出す
            $hasHandlingMethod = false;
            // アノテーションを利用してAOPを実行
            $annotation = new Annotation(STREAM_CLASSPATH . $this->controller());
            $methodAnnotations = $annotation->methods("@Error");
            foreach ($methodAnnotations as $methodAnnotation) {
                // @Error("Validate")のみ抽出
                // 複数のメソッドに対してアノテーションを定義可能とする
                if ($methodAnnotation->value === "Validate") {
                    if ($class->hasMethod($methodAnnotation->methodName)) {
                        $hasHandlingMethod = true;
                        $method = $class->getMethod($methodAnnotation->methodName);
                        $method->invoke($instance, array(
                            "class" => $this->controller(),
                            "method" => $this->action(),
                            "error" => $validator->getError()
                        ));
                    }
                }
            }
            // バリデーションエラーハンドリングメソッドがない場合、例外を出力
            if (!$hasHandlingMethod) {
                throw $e;
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