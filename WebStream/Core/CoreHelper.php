<?php
namespace WebStream\Core;

use WebStream\Module\Utility;
use WebStream\Module\Container;
use WebStream\Module\Security;
use WebStream\Module\Logger;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

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
     * @var Container DIコンテナ
     */
    private $container;

    /**
     * @var array<mixed> カスタムアノテーション
     */
    protected $annotation;

    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
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
     * 初期化処理
     * @Inject
     * @Filter(type="initialize")
     */
    public function __initialize(Container $container)
    {
    }

    /**
     * カスタムアノテーション情報を設定する
     * @param array<mixed> カスタムアノテーション情報
     */
    final public function __customAnnotation(array $annotation)
    {
        $this->annotation = $annotation;
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

    /**
     * 非同期処理を実行する
     * @param string パス
     * @param string DOMID
     * @return string JavaScript文字列
     */
    public function async($path, $id)
    {
        $safetyPath = str_replace('\\', '', $this->encodeJavaScript($path));
        $url = "//" . $this->container->request->server("HTTP_HOST") . $this->container->request->getBaseURL() . $safetyPath;

        return "<script type='text/javascript'>" . $this->asyncHelperCode($url, $id) . "</script>";
    }
}
