<?php
namespace WebStream\Core;

use WebStream\Module\Logger;
use WebStream\Module\Container;
use WebStream\Module\Utility;
use WebStream\Template\ITemplateEngine;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

/**
 * CoreViewクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/12
 * @version 0.4
 */
class CoreView implements CoreInterface
{
    use Utility;

    /**
     * @var Request リクエスト
     */
    private $request;

    /**
     * @var Response レスポンス
     */
    private $response;

    /**
     * @var ITemplateEngine テンプレートエンジン
     */
    private $templateEngine;

    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        Logger::debug("View start.");
        $this->request  = $container->request;
        $this->response = $container->response;
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        Logger::debug("View end.");
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
        $mimeType = $params["mimeType"] ?: "html";
        $this->outputHeader($mimeType);

        // HTML,XML以外はテンプレートを使用しない
        if ($mimeType !== "html" && $mimeType !== "xml") {
            Logger::debug("Only html or xml draw view template.");

            return;
        }

        if ($this->templateEngine !== null) {
            $this->templateEngine->render($params);
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
        $this->response->setType($type);
    }

    /**
     * publicディレクトリにある静的ファイルを表示する
     * @param String ファイルパス
     */
    final public function __file($filepath)
    {
        if (preg_match('/\/views\/'.STREAM_VIEW_PUBLIC.'\/img\/.+\.(?:jp(?:e|)g|png|bmp|(?:tif|gi)f)$/i', $filepath) ||
            preg_match('/\/views\/'.STREAM_VIEW_PUBLIC.'\/css\/.+\.css$/i', $filepath) ||
            preg_match('/\/views\/'.STREAM_VIEW_PUBLIC.'\/js\/.+\.js$/i', $filepath)) { // 画像,css,jsの場合
            $this->display($filepath);
        } elseif (preg_match('/\/views\/'.STREAM_VIEW_PUBLIC.'\/file\/.+$/i', $filepath)) { // それ以外のファイル
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
        $this->response->displayFile($filename);
    }

    /**
     * ファイルをダウンロードする
     * @param string ファイルパス
     */
    final private function download($filename)
    {
        $userAgent = $this->request->userAgent();
        $this->response->downloadFile($filename, $userAgent);
    }
}
