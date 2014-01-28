<?php
namespace WebStream\Core;

use WebStream\Module\Container;
use WebStream\Delegate\Resolver;
use WebStream\Module\Logger;
use WebStream\Module\Utility;

use WebStream\Exception\ApplicationException;
use WebStream\Exception\RouterException;
use WebStream\Exception\ResourceNotFoundException;
use WebStream\Exception\ClassNotFoundException;
use WebStream\Exception\MethodNotFoundException;
use WebStream\Exception\AnnotationException;
use WebStream\Exception\CsrfException;
use WebStream\Exception\InvalidRequestException;
use WebStream\Exception\ValidateException;
use WebStream\Exception\ForbiddenAccessException;
use WebStream\Exception\SessionTimeoutException;
use WebStream\Exception\DatabaseException;
use WebStream\Exception\IOException;

/**
 * Applicationクラス
 * @author Ryuichi Tanaka
 * @since 2011/08/19
 * @version 0.4
 */
class Application
{
    use Utility;

    /** アプリケーションファイルディレクトリ */
    private $app_dir = "app";
    /** アプリケーションルートディレクトリ */
    private $app_root = "";
    /** DocumentRoot */
    private $documentRoot;
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
        Logger::debug("Application start");
        $this->container = $container;
        $this->response  = $container->response;
        $this->request   = $container->request;
    }

    /**
     * アプリケーション終了時の処理
     */
    public function __destruct()
    {
        Logger::debug("Application end");
    }

    /**
     * 内部で使用する定数を定義
     */
    private function init()
    {
        /** streamのバージョン定義 */
        define('STREAM_VERSION', '0.4.0');
        /** プロジェクトディレクトリの絶対パスを定義 */
        define('STREAM_ROOT', $this->getRoot());
        /** アプリケーションディレクトリ */
        define('STREAM_APP_DIR', $this->app_dir); // 削除？予定
        /** アプリケーションルートパス */
        define('STREAM_APP_ROOT', $this->container->applicationRoot);
        /** publicディレクトリ */
        define('STREAM_VIEW_SHARED', "_shared");
        define('STREAM_VIEW_PUBLIC', "_public");
        define('STREAM_VIEW_CACHE', "_cache");
        /** キャッシュprefix */
        define('STREAM_CACHE_PREFIX', "webstream-cache-");
    }

    /**
     * ドキュメントルートパスを設定する
     * @param string ドキュメントルートパス
     */
    public function documentRoot($path)
    {
        $this->request->setDocumentRoot($path);
    }

    /**
     * アプリケーションを起動する
     */
    public function run()
    {
        try {
            $this->init();
            $this->resolver = new Resolver($this->container);
            $this->resolver->run(); // MVCレイヤへのリクエストの振り分けを実行する
        } catch (ApplicationException $e) {
            // アプリケーション内部エラーの場合は500
            Logger::error($e->getMessage(), $e->getTraceAsString());
            $this->response->move(500);
        } catch (CsrfException $e) {
            // CSRFエラーの場合は400
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->response->move(400);
            }
        } catch (SessionTimeoutException $e) {
            // セッションタイムアウトの場合は404
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->response->move(404);
            }
        } catch (ClassNotFoundException $e) {
            // 存在しないクラスアクセスの場合は500
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->response->move(500);
            }
        } catch (MethodNotFoundException $e) {
            // 存在しないメソッドアクセスの場合は500
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->response->move(500);
            }
        } catch (InvalidRequestException $e) {
            // 許可されないメソッドの場合は405
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->response->move(405);
            }
        } catch (ForbiddenAccessException $e) {
            // アクセス禁止の場合は403
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->response->move(403);
            }
        } catch (ResourceNotFoundException $e) {
            // リソース(URI)が見つからない場合は404
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->response->move(404);
            }
        } catch (AnnotationException $e) {
            // アノテーションエラーの場合は500
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->response->move(500);
            }
        } catch (ValidateException $e) {
            // バリデーションエラーの場合は422
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->response->move(422);
            }
        } catch (RouterException $e) {
            // ルーティング解決失敗の場合は500
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->response->move(500);
            }
        } catch (DatabaseException $e) {
            // データベースエラーの場合は500
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->response->move(500);
            }
        } catch (IOException $e) {
            // 入出力エラーの場合は500
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->response->move(500);
            }
        } catch (\RuntimeException $e) {
            // OutOfBoundsException, CollectionExceptionは500だが復帰可能
            Logger::error($e->getMessage(), $e->getTraceAsString());
            if (!$this->handle($e)) {
                $this->response->move(500);
            }
        }
    }

    /**
     * エラー処理のハンドリングチェック
     * @param object 例外オブジェクト
     * @return boolean 例外ハンドリング可否
     */
    private function handle(\Exception $e)
    {
        try {
            return $this->resolver->handle($e);
        } catch (AnnotationException $e) {
            // アノテーションエラーの場合は500
            Logger::error($e->getMessage(), $e->getTraceAsString());
            $this->response->move(500);
        } catch (ApplicationException $e) {
            // アプリケーション内部エラーの場合は500
            Logger::error($e->getMessage(), $e->getTraceAsString());
            $this->response->move(500);
        } catch (\Exception $e) {
            // 開発者由来の例外は全て500
            Logger::error($e->getMessage(), $e->getTraceAsString());
            $this->response->move(500);
        }
    }
}
