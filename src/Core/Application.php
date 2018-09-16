<?php
namespace WebStream\Core;

use WebStream\Container\Container;
use WebStream\Delegate\Resolver;
use WebStream\Exception\ApplicationException;
use WebStream\Exception\SystemException;
use WebStream\Exception\DelegateException;
use WebStream\Module\ServiceLocator;

/**
 * Applicationクラス
 * @author Ryuichi Tanaka
 * @since 2011/08/19
 * @version 0.7
 */
class Application
{
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
        register_shutdown_function([&$this, 'shutdownHandler']);
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
                $this->container->logger->error($e->getExceptionAsString());
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
                $this->container->logger->fatal($e->getExceptionAsString());
                $this->container->response->move(500);
            }
        } catch (SystemException $e) {
            // 内部例外の内、ハンドリング不許可の例外
            $this->container->logger->fatal($e->getExceptionAsString());
            $this->container->response->move($e->getCode());
        }
    }

    /**
     * 例外捕捉不可な異常時のアプリケーション終了処理
     */
    public function shutdownHandler()
    {
        if ($error = error_get_last()) {
            $errorMsg = $error['message'] . " " . $error['file'] . "(" . $error['line'] . ")";
            switch ($error['type']) {
                case E_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                case E_RECOVERABLE_ERROR:
                    $this->container->logger->fatal($errorMsg);
                    $this->container->logger->enableDirectWrite();
                    $this->container->response->move(500);
                    break;
                case E_PARSE:
                    $this->container->logger->error($errorMsg);
                    $this->container->logger->enableDirectWrite();
                    $this->container->response->move(500);
                    break;
                case E_WARNING:
                case E_CORE_WARNING:
                case E_COMPILE_WARNING:
                case E_USER_WARNING:
                case E_STRICT:
                case E_NOTICE:
                case E_USER_NOTICE:
                case E_DEPRECATED:
                case E_USER_DEPRECATED:
                    $this->container->logger->warn($errorMsg);
                    $this->container->logger->enableDirectWrite();
                    break;
            }
        }
    }
}
