<?php
namespace WebStream\Core;

use WebStream\Module\Container;
use WebStream\Module\Utility\LoggerUtils;
use WebStream\Delegate\Resolver;
use WebStream\Exception\ApplicationException;
use WebStream\Exception\SystemException;
use WebStream\Exception\DelegateException;
use WebStream\DI\ServiceLocator;

/**
 * Applicationクラス
 * @author Ryuichi Tanaka
 * @since 2011/08/19
 * @version 0.7
 */
class Application
{
    use LoggerUtils;

    /**
     * @var Container DIコンテナ
     */
    private $container;

    /**
     * アプリケーション共通で使用するクラスを初期化する
     * @param Container DIコンテナ
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->container->logger->debug("Application start");
    }

    /**
     * アプリケーション終了時の処理
     */
    public function __destruct()
    {
        $this->container->logger->debug("Application end");
    }

    /**
     * アプリケーションを起動する
     */
    public function run()
    {
        try {
            $resolver = new Resolver($this->container);
            $resolver->runController(); // MVCレイヤへのリクエストの振り分けを実行する
        } catch (ApplicationException $e) {
            // 内部例外の内、ハンドリングを許可している例外
            try {
                $isHandled = false;
                if ($e instanceof DelegateException) {
                    $isHandled = $e->isHandled();
                    $e = $e->getOriginException();
                }
                if (!$isHandled) {
                    $this->container->response->move($e->getCode());
                }
            } catch (\Exception $e) {
                // 開発者由来の例外は全て500
                $this->container->logger->error($this->addStackTrace($e->getMessage(), $e->getTraceAsString()));
                $this->container->response->move(500);
            }
        } catch (SystemException $e) {
            // 内部例外の内、ハンドリング不許可の例外
            $this->container->response->move($e->getCode());
        }
    }
}
