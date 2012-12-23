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
    /** Request */
    private $request;
    /** Response */
    private $response;
    /** Resolver */
    private $resolver;
    /** リソースキャッシュパラメータ */
    private $cache = array();
    
    /**
     * アプリケーション共通で使用するクラスを初期化する
     */
    public function __construct() {
        $this->request = new Request();
        $this->response = new Response();
        ob_start();
        ob_implicit_flush(false);
    }
    
    /**
     * アプリケーション終了時の処理
     */
    public function __destruct() {
        $buffer = ob_get_clean();
        $this->response->setBody($buffer);
        $this->response->send();
        $this->responseCache($buffer);
    }
    
    /**
     * 内部で使用する定数を定義
     */
    private function init() {
        /** streamのバージョン定義 */
        define('STREAM_VERSION', '0.3.15');
        /** クラスパス */
        define('STREAM_CLASSPATH', '\\WebStream\\');
        /** プロジェクトディレクトリの絶対パスを定義 */
        define('STREAM_ROOT', Utility::getRoot());
        /** アプリケーションディレクトリ */
        define('STREAM_APP_DIR', $this->app_dir);
        /** ドキュメントルートからプロジェクトディレクトリへのパスを定義 */
        define('STREAM_BASE_URI', $this->request->getBaseURL());
        define('STREAM_ROUTING_PATH', $this->request->getPathInfo());
        define('STREAM_QUERY_STRING', $this->request->getQueryString());
        /** publicディレクトリ */
        define('STREAM_VIEW_SHARED', "_shared");
        define('STREAM_VIEW_PUBLIC', "_public");
        define('STREAM_VIEW_CACHE', "_cache");
        /** レスポンスキャッシュID */
        define('STREAM_RESPONSE_CACHE_ID', 
               md5(STREAM_BASE_URI . STREAM_ROUTING_PATH . STREAM_QUERY_STRING));
    }
    
    /**
     * アプリケーションを起動する
     */
    public function run() {
        $this->init();
        $this->responseCache();
        $this->resolver = new Resolver($this->request, $this->response);
        try {
            // ルーティングを解決する
            $router = new Router($this->request);
            // MVCレイヤへのリクエストの振り分けを実行する
            $this->resolver->setRouter($router);
            $this->resolver->run();
        }
        // CSRFエラーの場合は400
        catch (CsrfException $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->move(400);
            }
        }
        // セッションタイムアウトの場合は404
        catch (SessionTimeoutException $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->move(404);
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
     * エラー処理のハンドリングチェック
     * @param Object エラーオブジェクト
     * @param Array エラー内容
     * @return Boolean ハンドリングするかどうか
     */
    private function handle($errorObj, $errorParams = array()) {
        return $this->resolver->handle($errorObj, $errorParams);
    }
    
    /**
     * ステータスコードに合わせた画面に遷移する
     * @param String ステータスコード
     */
    private function move($statusCode) {
        $this->response->move($statusCode);
    }
    
    /**
     * レスポンスキャッシュを設定する
     * @param String キャッシュデータ
     */
    private function responseCache($data = null) {
        var_dump($data);
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
}