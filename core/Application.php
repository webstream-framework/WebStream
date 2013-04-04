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
    /** Container */
    private $container;
    
    /**
     * アプリケーション共通で使用するクラスを初期化する
     * @param Object DIコンテナ
     */
    public function __construct(Container $container) {
        $this->container = $container;
        $this->request   = $container->request;
        $this->response  = $container->response;
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
        $this->resolver->responseCache($buffer);
    }
    
    /**
     * 内部で使用する定数を定義
     */
    private function init() {
        /** streamのバージョン定義 */
        define('STREAM_VERSION', '0.3.17');
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
        $this->resolver = new Resolver($this->container);
        $this->resolver->responseCache();
        try {
            // MVCレイヤへのリクエストの振り分けを実行する
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
            if (!$this->handle($e, $this->resolver->getValidateErrors())) {
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
        $this->resolver->move($statusCode);
    }
}