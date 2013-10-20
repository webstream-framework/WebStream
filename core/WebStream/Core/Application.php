<?php
namespace WebStream\Core;

use WebStream\Module\Container;
use WebStream\Module\Utility;
use WebStream\Delegate\Resolver;
use WebStream\Module\Logger;

use WebStream\Exception\RouterException;
use WebStream\Exception\ResourceNotFoundException;
use WebStream\Exception\ClassNotFoundException;
use WebStream\Exception\MethodNotFoundException;
use WebStream\Exception\AnnotationException;
use WebStream\Exception\CsrfException;

/**
 * Applicationクラス
 * @author Ryuichi Tanaka
 * @since 2011/08/19
 * @version 0.4
 */
class Application
{
    use Utility;

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
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->request   = $container->request;
        $this->response  = $container->response;
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * アプリケーション終了時の処理
     */
    public function __destruct()
    {
        $buffer = ob_get_clean();
        $this->response->setBody($buffer);
        $this->response->send();
        //$this->resolver->responseCache($buffer);
    }

    /**
     * 内部で使用する定数を定義
     */
    private function init()
    {
        /** streamのバージョン定義 */
        define('STREAM_VERSION', '0.4.0');
        /** クラスパス */
        //define('STREAM_CLASSPATH', '\\WebStream\\');
        /** プロジェクトディレクトリの絶対パスを定義 */
        define('STREAM_ROOT', $this->getRoot());
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
        /** キャッシュprefix */
        define('STREAM_CACHE_PREFIX', "webstream-cache-");
        /** レスポンスキャッシュID */
        define('STREAM_RESPONSE_CACHE_ID',
               md5(STREAM_BASE_URI . STREAM_ROUTING_PATH . STREAM_QUERY_STRING));
    }

    /**
     * アプリケーションを起動する
     */
    public function run()
    {
        $this->init();
        $this->resolver = new Resolver($this->container);
        //$this->resolver->responseCache();
        try {
            // MVCレイヤへのリクエストの振り分けを実行する
            $this->resolver->run();
        } catch (CsrfException $e) {
            // CSRFエラーの場合は400
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->move(400);
            }
        } catch (SessionTimeoutException $e) {
            // セッションタイムアウトの場合は404
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->move(404);
            }
        } catch (ClassNotFoundException $e) {
            // 存在しないクラスアクセスの場合は500
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->move(500);
            }
        } catch (MethodNotFoundException $e) {
            // 存在しないクラスアクセスの場合は500
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->move(500);
            }
        } catch (MethodNotAllowedException $e) {
            // 許可されないメソッドの場合は405
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->move(405);
            }
        } catch (ForbiddenAccessException $e) {
            // アクセス禁止の場合は403
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->move(403);
            }
        } catch (ResourceNotFoundException $e) {
            // リソース(URI)が見つからない場合は404
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->move(404);
            }
        } catch (AnnotationException $e) {
            // アノテーションエラーの場合は500
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->move(500);
            }
        } catch (ValidateException $e) {
            // バリデーションエラーの場合は422
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e, $this->resolver->getValidateErrors())) {
                $this->move(422);
            }
        } catch (RouterException $e) {
            // ルーティング解決失敗の場合は500
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e, $this->resolver->getValidateErrors())) {
                $this->move(500);
            }
        }
    }

    /**
     * エラー処理のハンドリングチェック
     * @param Object エラーオブジェクト
     * @param Array エラー内容
     * @return Boolean ハンドリングするかどうか
     */
    private function handle($errorObj, $errorParams = [])
    {
        return $this->resolver->handle($errorObj, $errorParams);
    }

    /**
     * ステータスコードに合わせた画面に遷移する
     * @param String ステータスコード
     */
    private function move($statusCode)
    {
        $this->resolver->move($statusCode);
    }
}
