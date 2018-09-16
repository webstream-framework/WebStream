<?php
namespace WebStream\Core;

use WebStream\Container\Container;
use WebStream\Annotation\Attributes\Filter;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Util\ApplicationUtils;
use WebStream\Util\Security;

/**
 * CoreHelperクラス
 * @author Ryuichi TANAKA.
 * @since 2011/11/30
 * @version 0.4
 */
class CoreHelper implements CoreInterface, IAnnotatable
{
    use ApplicationUtils;

    /**
     * @var Container DIコンテナ
     */
    private $container;

    /**
     * @var array<mixed> カスタムアノテーション
     */
    protected $annotation;

    /**
     * @var LoggerAdapter ロガー
     */
    protected $logger;

    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->logger = $container->logger;
        $this->logger->debug("Helper start.");
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        $this->logger->debug("Helper end.");
    }

    /**
     * {@inheritdoc}
     * @Filter(type="initialize")
     */
    public function __initialize(Container $container)
    {
    }

    /**
    * {@inheritdoc}
     */
    public function __customAnnotation(array $annotation)
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
        $url = "//" . $this->container->request->httpHost . $this->container->request->baseUri . $safetyPath;

        return "<script type='text/javascript'>" . $this->asyncHelperCode($url, $id) . "</script>";
    }
}
