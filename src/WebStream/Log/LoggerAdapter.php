<?php
namespace WebStream\Log;

/**
 * LoggerAdapterクラス
 * PSR-3実装のロガーをWebStreamロガーに委譲
 * @author Ryuichi Tanaka
 * @since 2015/12/03
 * @version 0.7
 */
class LoggerAdapter implements \Psr\Log\LoggerInterface
{
    use \Psr\Log\LoggerTrait;

    /**
     * @var Logger ロガーインスタンス
     */
    private $logger;

    /**
     * コンストラクタ
     */
    public function __construct(\WebStream\Log\Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * PSR-3ロガーに定義されていないログレベルの呼び出しを受ける
     * @param string $name ログレベル
     * @param array $arguments 引数
     */
    public function __call($name, $arguments)
    {
        $message = null;
        if (array_key_exists(0, $arguments)) {
            $message = $arguments[0];
        }

        if (array_key_exists(1, $arguments)) {
            $this->log($name, $message, $arguments[1]);
        } else {
            $this->log($name, $message, []);
        }
    }

    /**
     * Logs with an arbitrary level.
     * @param int $level ログレベル
     * @param string $message メッセージ
     * @param array $context 埋め込み値リスト
     */
    public function log($level, $message, array $context = [])
    {
        $this->logger->write($level, $message, $context);
    }

    /**
     * 遅延書き出しを有効にする
     */
    public function enableLazyWrite()
    {
        $this->logger->lazyWrite();
    }

    /**
     * 即時書き出しを有効にする
     */
    public function enableDirectWrite()
    {
        $this->logger->directWrite();
    }
}
