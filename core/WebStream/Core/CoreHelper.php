<?php
namespace WebStream\Core;

use WebStream\Module\Utility;
use WebStream\Module\Container;
use WebStream\Module\Security;
use WebStream\Module\Logger;

/**
 * CoreHelperクラス
 * @author Ryuichi TANAKA.
 * @since 2011/11/30
 * @version 0.4
 */
class CoreHelper implements CoreInterface
{
    use Utility;

    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        Logger::debug("Helper start.");
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        Logger::debug("Helper end.");
    }

    /**
     * 安全なHTMLに変換する
     * @param string HTML文字列
     * @return string 安全なHTML文字列
     */
    public function encodeHtml($str)
    {
        return Security::safetyOut($str);
    }

    /**
     * 安全なJavaScriptに変換する
     * @param string JavaScript文字列
     * @return string 安全なJavaScript文字列
     */
    public function encodeJavaScript($str)
    {
        return Security::safetyOutJavaScript($str);
    }
}
