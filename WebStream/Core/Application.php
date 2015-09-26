<?php
namespace WebStream\Core;

use WebStream\Module\Container;
use WebStream\Module\Logger;
use WebStream\Module\Utility;
use WebStream\Delegate\Resolver;
use WebStream\Exception\ApplicationException;
use WebStream\Exception\SystemException;
use WebStream\Exception\DelegateException;

/**
 * Applicationクラス
 * @author Ryuichi Tanaka
 * @since 2011/08/19
 * @version 0.4
 */
class Application
{
    use Utility;

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
        define('STREAM_APP_DIR', $this->container->applicationDir);
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
     * アプリケーションを起動する
     */
    public function run()
    {
        try {
            $this->init();
            $this->resolver = new Resolver($this->container);
            $this->resolver->runController(); // MVCレイヤへのリクエストの振り分けを実行する
        } catch (ApplicationException $e) {
            // 内部例外の内、ハンドリングを許可している例外
            try {
                $isHandled = false;
                if ($e instanceof DelegateException) {
                    $isHandled = $e->isHandled();
                    $e = $e->getOriginException();
                }
                if (!$isHandled) {
                    $this->response->move($e->getCode());
                }
            } catch (\Exception $e) {
                // 開発者由来の例外は全て500
                Logger::error($e->getMessage(), $e->getTraceAsString());
                $this->response->move(500);
            }
        } catch (SystemException $e) {
            // 内部例外の内、ハンドリング不許可の例外
            $this->response->move($e->getCode());
        }
    }
}
