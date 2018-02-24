<?php
namespace WebStream\Core;

use WebStream\Annotation\Attributes\Filter;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Container\Container;
use WebStream\DI\Injector;
use WebStream\Template\ITemplateEngine;
use WebStream\Util\CommonUtils;

/**
 * CoreViewクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/12
 * @version 0.7
 */
class CoreView implements CoreInterface, IAnnotatable
{
    use Injector, CommonUtils;

    /**
     * @var Container 依存コンテナ
     */
    private $container;

    /**
     * @var ITemplateEngine テンプレートエンジン
     */
    private $templateEngine;

    /**
     * @var LoggerAdapter ロガー
     */
    private $logger;

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        $this->logger->debug("View end.");
    }

    /**
     * {@inheritdoc}
     * @Filter(type="initialize")
     */
    public function __initialize(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function __customAnnotation(array $annotation)
    {
    }

    /**
     * テンプレートエンジンを設定する
     * @param ITemplateEngine テンプレートエンジン
     */
    public function setTemplateEngine(ITemplateEngine $templateEngine = null)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * テンプレートを描画する
     * @param array<string> パラメータ
     */
    public function draw(array $params)
    {
        $mimeType = $params["mimeType"];
        $this->outputHeader($mimeType);

        // HTML,XML以外はテンプレートを使用しない
        if ($mimeType !== 'html' && $mimeType !== 'xml') {
            $this->logger->debug("Only html or xml draw view template.");

            return;
        }

        if ($this->templateEngine !== null) {
            $this->templateEngine->render($params);
            if ($this->templateEngine instanceof \WebStream\Template\Basic) {
                $this->templateEngine->renderHelper($params);
            }
        }
    }

    /**
     * テンプレートキャッシュを作成する
     * @param string テンプレートファイルパス
     * @param string 保存データ
     * @param integer 有効期限
     */
    public function templateCache($filepath, $cacheData, $cacheTime)
    {
        if ($this->templateEngine instanceof \WebStream\Template\Basic) {
            $this->templateEngine->cache($filepath, $cacheData, $cacheTime);
        }
    }

    /**
     * 共通ヘッダを出力する
     * @param String ファイルタイプ
     */
    private function outputHeader($type)
    {
        $this->container->response->setType($type);
    }

    /**
     * publicディレクトリにある静的ファイルを表示する
     * @param String ファイルパス
     */
    final public function __file($filepath)
    {
        $publicDir = $this->container->applicationInfo->publicDir;
        if (preg_match('/\/views\/' . $publicDir . '\/img\/.+\.(?:jp(?:e|)g|png|bmp|(?:tif|gi)f)$/i', $filepath) ||
            preg_match('/\/views\/' . $publicDir . '\/css\/.+\.css$/i', $filepath) ||
            preg_match('/\/views\/' . $publicDir . '\/js\/.+\.js$/i', $filepath)) { // 画像,css,jsの場合
            $this->display($filepath);
        } elseif (preg_match('/\/views\/' . $publicDir . '\/file\/.+$/i', $filepath)) { // それ以外のファイル
            $this->download($filepath);
        } else { // 全てのファイル
            $this->display($filepath);
        }
    }

    /**
     * 画像、CSS、JavaScriptファイルを表示する
     * @param string ファイルパス
     */
    final private function display($filename)
    {
        $this->container->response->displayFile($filename);
    }

    /**
     * ファイルをダウンロードする
     * @param string ファイルパス
     */
    final private function download($filename)
    {
        $userAgent = $this->container->request->userAgent();
        $this->container->response->downloadFile($filename, $userAgent);
    }
}
