<?php
/**
 * Applicationクラス
 * @author Ryuichi Tanaka
 * @since 2011/08/19
 */
class Application {
    private $app_dir = "app";
    /** ルーティング解決後のパラメータ */
    private $route;
    
    /**
     * アプリケーション共通で使用するクラスを初期化する
     */
    public function __construct() {
        /** streamのバージョン定義 */
        define('STREAM_VERSION', '0.1.7');
    }
    
    /**
     * 内部で使用する定数を定義
     */
    private function init() {
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
        // それ以外のエラーは500
        catch (Exception $e) {
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
        $class = new ReflectionClass($this->controller());
        $instance = $class->newInstance();
        // initialize
        $initialize = $class->getMethod("initialize");
        $initialize->invoke($instance);
        // before_filter
        $before_filter = $class->getMethod("before");
        $before_filter->invoke($instance);
        // action
        $action = $class->getMethod($this->action());
        $action->invoke($instance, safetyIn($this->params()));
        // after_filter
        $after_filter = $class->getMethod("after");
        $after_filter->invoke($instance);
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
            $controller = preg_replace_callback('/_(?=[a-z])(.+?)/', create_function(
                '$matches',
                'return ucfirst($matches[1]);'
            ), $this->route->controller());
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
            $action = preg_replace_callback('/_(?=[a-z0-9])(.+?)/', create_function(
                '$matches',
                'return ucfirst($matches[1]);'
            ), $this->route->action());
        }
        return $action;
    }
    
    /**
     * パラメータを返却する
     * @return Array パラメータ
     */
    private function params() {
        return $this->route->params();
    }
    
    private function staticFile() {
        return $this->route->staticFile();
    }
}