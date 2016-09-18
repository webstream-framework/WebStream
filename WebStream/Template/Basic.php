<?php
namespace WebStream\Template;

use WebStream\IO\File;
use WebStream\IO\Reader\FileReader;
use WebStream\IO\Writer\FileWriter;
use WebStream\Module\Utility\CommonUtils;
use WebStream\Module\Utility\ApplicationUtils;
use WebStream\Module\Utility\SecurityUtils;
use WebStream\Container\Container;
use WebStream\Cache\Driver\CacheDriverFactory;
use WebStream\Exception\Extend\ResourceNotFoundException;

/**
 * Basic
 * @author Ryuichi Tanaka
 * @since 2015/03/18
 * @version 0.7
 */
class Basic implements ITemplateEngine
{
    use CommonUtils, ApplicationUtils, SecurityUtils;

    /** HTML記法 */
    const TEMPLATE_MARK_HTML       = '%H';
    /** PHP記法 */
    const TEMPLATE_MARK_PHP        = '%P';
    /** JavaScript記法 */
    const TEMPLATE_MARK_JAVASCRIPT = '%J';
    /** XML記法 */
    const TEMPLATE_MARK_XML        = '%X';
    /** Template記法 */
    const TEMPLATE_MARK_TEMPLATE   = '%T';

    /**
     * @var Container 依存コンテナ
     */
    private $container;

    /**
     * @var Session セッション
     */
    private $session;

    /**
     * @var int Unixタイムスタンプ
     */
    private $timestamp;

    /**
     * @var Logger ロガー
     */
    private $logger;

    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->session  = $container->session;
        $this->timestamp = 0;
        $this->logger = $container->logger;
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $params)
    {
        $mimeType = $params["mimeType"];
        $params = ["model" => $params["model"], "helper" => $params["helper"]];
        $dirname = $this->camel2snake($this->container->router->pageName);

        $templateFile = new File($this->container->applicationInfo->applicationRoot . "/app/views/" . $dirname . "/" . $this->container->filename);
        $sharedFile = new File($this->container->applicationInfo->applicationRoot . "/app/views/" . $this->container->applicationInfo->sharedDir . "/" . $this->container->filename);

        $file = $templateFile->exists() ? $templateFile : ($sharedFile->exists() ? $sharedFile : null);
        if ($file === null) {
            $errorMessage = "Invalid template file path: " . $templateFile->getFilePath() . " or " .  $sharedFile->getFilePath();
            throw new ResourceNotFoundException($errorMessage);
        }

        // テンプレートファイルの最新の変更日時を取得
        $timestamp = $file->lastModified();
        if ($timestamp > $this->timestamp) {
            $this->timestamp = $timestamp;
        }

        // テンプレートが見つからない場合は500になるのでエラー処理は不要
        $reader = new FileReader($file);
        $content = $reader->read();
        $this->replaceTemplateMark($content, $mimeType);

        // テンプレートファイルをコンパイルし一時ファイルを作成
        $tmpFile = new File($this->getTemporaryDirectory() . "/" . $this->getRandomstring(30));
        $writer = new FileWriter($tmpFile);
        $writer->write($content);
        $writer->close();
        $this->logger->debug("Write temporary template file: " . $tmpFile->getFilePath());
        $this->logger->debug("Compiled template file size: " . $tmpFile->size());

        $params["__params__"] = $params;
        $params["__mimeType__"] = $mimeType;
        $this->outputHTML($tmpFile->getFilePath(), $params);

        $tmpFile->delete();
    }

    /**
     * HelperのPHPコードを有効にしたHTML文字列を出力する
     * @param array<string> Viewパラメータ
     */
    public function renderHelper(array $params)
    {
        $mimeType = $params["mimeType"];

        // Helperで出力されるコードを有効にするためバッファリングを取得、終了する
        $content = ob_get_clean();

        // バッファリングを再開
        $this->container->response->start();
        $isReplaced = $this->replaceTemplateMark($content, $mimeType);

        $params = ["model" => $params["model"], "helper" => $params["helper"]];

        // CSRFトークンを付与
        // CSRFチェックが実行される前に非同期でリクエストがあった場合を考慮して
        // CSRFトークンは削除しない
        if (preg_match('/<form.*?>.*?<\/form>/is', $content)) {
            $csrfToken = sha1($this->session->id() . microtime());
            $this->session->set($this->getCsrfTokenKey(), $csrfToken);
            $this->addToken($content, $csrfToken);
        }

        // テンプレートファイルをコンパイルし一時ファイルを作成
        $tmpFile = new File($this->getTemporaryDirectory() . "/" . $this->getRandomstring(30));
        $writer = new FileWriter($tmpFile);
        $writer->write($content);
        $writer->close();
        $this->logger->debug("Write temporary template file: " . $tmpFile->getFilePath());
        $this->logger->debug("Compiled template file size: " . $tmpFile->size());

        $params["__params__"] = $params;
        $params["__mimeType__"] = $mimeType;
        $this->outputHTML($tmpFile->getFilePath(), $params);

        $tmpFile->delete();

        // テンプレート記法がある場合、再帰的に展開していく
        if ($isReplaced) {
            $this->renderHelper([
                "mimeType" => $mimeType,
                "model" => $params["model"],
                "helper" => $params["helper"]
            ]);
        }
    }

    /**
     * テンプレートを描画する
     * @param string テンプレートファイル名
     * @param array<mixed> パラメータ
     * @param string mime type
     */
    public function draw($filename, $params, $mimeType)
    {
        $this->container->filename = $filename;
        $params["mimeType"] = $mimeType;
        $this->render($params);
    }

    /**
     * テンプレートキャッシュを作成する
     * @param string テンプレートファイルパス
     * @param string 保存データ
     * @param integer 有効期限
     * @throws IOException
     */
    public function cache($filename, $data, $expire)
    {
        $cacheDir = $this->container->applicationInfo->applicationRoot . "/app/views/" . $this->container->applicationInfo->cacheDir;
        $factory = new CacheDriverFactory();
        $config = new Container(false);
        $config->cacheDir = $cacheDir;
        $config->classPrefix = "view_cache";
        $cache = $factory->create("WebStream\Cache\Driver\TemporaryFile", $config);
        $cache->inject('logger', $this->logger);

        $file = new File($cacheDir . "/" . $filename . ".cache");
        if (!$file->exists() || $this->timestamp > $file->lastModified()) {
            if ($cache->add($filename, $data, $expire)) {
                $this->logger->debug("Write template cache file: " . $file->getFilePath());
            } else {
                throw new IOException("File write failure: " . $file->getFilePath());
            }
        }
    }

    /**
     * テンプレートを描画する
     * @param string テンプレートファイル
     * @param mixed 展開するパラメータ
     */
    private function outputHTML($template, $params)
    {
        extract($params);
        $params = null;
        include($template);
    }

    /**
     * テンプレート記法を変換する
     * @param string 変換前出力内容
     * @param string mimeType
     * @return bool 変換されたらtrue
     */
    private function replaceTemplateMark(&$content, $mimeType)
    {
        $originContentHash = md5($content);

        $content = preg_replace_callback('/(%.{\$' . $this->getHelperVariableName() . '\->async\(.+?\)})/', function ($matches) {
            $asyncId = $this->getAsyncDomId();
            $context = preg_replace_callback('/\$' . $this->getHelperVariableName() . '->async\((.+?)\)/', function ($matches2) use ($asyncId) {
                return '$' . $this->getHelperVariableName() . '->async(' . $matches2[1] . ',\'' . $asyncId . '\')';
            }, $matches[1]);

            return "<div id='$asyncId'>$context</div>";
        }, $content);

        $content = preg_replace('/' . self::TEMPLATE_MARK_PHP . '\{(.*?)\}/', '<?php echo $1; ?>', $content);
        $content = preg_replace_callback('/' . self::TEMPLATE_MARK_TEMPLATE . '\{(.*?)\}/', function ($matches) {
            if (substr($matches[1], 0, 1) === '$') {
                return self::TEMPLATE_MARK_TEMPLATE . '{<?php echo ' . $matches[1] . ';?>}';
            } else {
                return '<?php $this->draw(\'' . $matches[1] . '\', $__params__, $__mimeType__); ?>';
            }
        }, $content);

        if ($mimeType === "xml") {
            $content = preg_replace('/' . self::TEMPLATE_MARK_XML . '\{(.*?)\}/', '<?php echo safetyOutXML($1); ?>', $content);
        } elseif ($mimeType === "html") {
            $content = preg_replace('/' . self::TEMPLATE_MARK_HTML . '\{(.*?)\}/', '<?php echo safetyOut($1); ?>', $content);
            $content = preg_replace('/' . self::TEMPLATE_MARK_JAVASCRIPT . '\{(.*?)\}/', '<?php echo safetyOutJavaScript($1); ?>', $content);
        }

        $replacedContentHash = md5($content);

        // XML開始タグのみ変換比較には使わない
        if ($mimeType === 'xml') {
            $content = preg_replace('/^<\?xml/', '<<?php ?>?xml', $content);
        }

        return $originContentHash !== $replacedContentHash;
    }

    /**
     * すべてのformタグにCSRF対策トークンを追加する
     * @param string HTML文字列の参照
     */
    private function addToken(&$content, $csrfToken)
    {
        // <meta>タグによるcharsetが指定されない場合は文字化けするのでその対策を行う
        $content = mb_convert_encoding($content, 'html-entities', "UTF-8");
        // DOMでformにアペンドする
        $doc = new \DOMDocument();
        // テンプレートがが断片でなく、完全の場合(layoutを使わずrenderだけで描画した場合)
        // 警告が出るが処理は正常に実行出来るので無視する
        @$doc->loadHTML($content);
        $nodeList = $doc->getElementsByTagName("form");
        $dummyValue = $this->getRandomString();
        $nodeLength = $nodeList->length;
        for ($i = 0; $i < $nodeLength; $i++) {
            $node = $nodeList->item($i);
            $method = $node->getAttribute("method");
            if (preg_match('/^post$/i', $method)) {
                $newNode = $doc->createElement("input");
                $newNode->setAttribute("type", "hidden");
                $newNode->setAttribute("name", $this->getCsrfTokenKey());
                $newNode->setAttribute("value", $dummyValue);
                $node->appendChild($newNode);
            }
        }
        if ($nodeLength !== 0) {
            $innerHTML = "";
            $bodyNodeList = $doc->getElementsByTagName("html");
            $bodyNode = $bodyNodeList->item(0);
            $children = $bodyNode->childNodes;
            foreach ($children as $child) {
                $tmp = new \DOMDocument();
                $tmp->formatOutput = true;
                $tmp->appendChild($tmp->importNode($child, true));
                $innerHTML .= trim($tmp->saveHTML());
            }
            $content = str_replace($dummyValue, $csrfToken, $innerHTML);
        }
        // 実体参照化をもとに戻す。
        $map = array('&gt;' => '>',
                     '&lt;' => '<',
                     '%20'  => ' ',
                     '%24'  => '$',
                     '%5C'  => '\\');

        // HTMLタグを補完する
        $content = <<< HTML
<!DOCTYPE html>
<html>
$content
</html>
HTML;

        $content = str_replace(array_keys($map), array_values($map), $content);
    }
}
