<?php
namespace WebStream\Template;

use WebStream\Module\Logger;
use WebStream\Module\Cache;
use WebStream\Module\Utility;
use WebStream\Module\Container;
use WebStream\Exception\Extend\ResourceNotFoundException;

/**
 * Basic
 * @author Ryuichi Tanaka
 * @since 2015/03/18
 * @version 0.4.0
 */
class Basic implements ITemplateEngine
{
    use Utility;

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
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->session  = $container->session;
        $this->timestamp = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $params)
    {
        $mimeType = $params["mimeType"];
        $params = ["model" => $params["model"], "helper" => $params["helper"]];
        $dirname = $this->camel2snake($this->container->router->routingParams()['controller']);

        $filepath = STREAM_APP_ROOT . "/app/views/" . $dirname . "/" . $this->container->filename;
        $sharedpath = STREAM_APP_ROOT . "/app/views/" . STREAM_VIEW_SHARED . "/" . $this->container->filename;

        $realpath = realpath($filepath) ?: realpath($sharedpath);

        if ($realpath === false) {
            throw new ResourceNotFoundException("Invalid template file path: " . safetyOut($filepath));
        }

        // テンプレートファイルの最新の変更日時を取得
        $timestamp = filemtime($realpath);
        if ($timestamp > $this->timestamp) {
            $this->timestamp = $timestamp;
        }

        // テンプレートが見つからない場合は500になるのでエラー処理は不要
        $content = file_get_contents($realpath);
        $content = preg_replace('/^<\?xml/', '<<?php ?>?xml', $content);

        $content = preg_replace_callback('/(%.{\$' . $this->getHelperVariableName() . '\->async\(.+?\)})/', function ($matches) {
            $asyncId = $this->getAsyncDomId();
            $context = preg_replace_callback('/\$' . $this->getHelperVariableName() . '->async\((.+?)\)/', function ($matches2) use ($asyncId) {
                return '$' . $this->getHelperVariableName() . '->async(' . $matches2[1] . ',\'' . $asyncId . '\')';
            }, $matches[1]);

            return "<div id='$asyncId'>$context</div>";
        }, $content);

        $content = preg_replace('/' . self::TEMPLATE_MARK_PHP . '\{(.*?)\}/', '<?php echo $1; ?>', $content);
        $content = preg_replace('/' . self::TEMPLATE_MARK_TEMPLATE . '\{(.*?)\}/', '<?php $this->draw("$1", $__params__, $__mimeType__); ?>', $content);

        if ($mimeType === "xml") {
            $content = preg_replace('/' . self::TEMPLATE_MARK_XML . '\{(.*?)\}/', '<?php echo safetyOutXML($1); ?>', $content);
        } elseif ($mimeType === "html") {
            $content = preg_replace('/' . self::TEMPLATE_MARK_HTML . '\{(.*?)\}/', '<?php echo safetyOut($1); ?>', $content);
            $content = preg_replace('/' . self::TEMPLATE_MARK_JAVASCRIPT . '\{(.*?)\}/', '<?php echo safetyOutJavaScript($1); ?>', $content);
            // formタグが含まれる場合はCSRFトークンを付与する
            if (preg_match('/<form.*?>.*?<\/form>/is', $content)) {
                $this->addToken($params, $content);
            } else {
                // formタグがない場合、CSRFトークンセッションは不要なので削除
                $this->session->delete($this->getCsrfTokenKey());
            }
        }

        // テンプレートファイルをコンパイルし一時ファイルを作成
        $temp = $this->getTemporaryDirectory() . "/" . $this->getRandomstring(30);
        $fileSize = file_put_contents($temp, $content, LOCK_EX);
        if ($fileSize === false) {
            throw new IOException("File write failure: " . $temp);
        } else {
            Logger::debug("Write temporary template file: " . $temp);
            Logger::debug("Compiled template file size: " . $fileSize);
        }

        $params["__params__"] = $params;
        $params["__mimeType__"] = $mimeType;
        $this->outputHTML($temp, $params);

        unlink($temp);
    }

    /**
     * 部分テンプレートを描画する
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
     */
    public function cache($filename, $data, $expire)
    {
        $cacheDir = STREAM_APP_ROOT . "/app/views/" . STREAM_VIEW_CACHE;
        $cache = new Cache($cacheDir);
        $filepath = $cacheDir . "/" . $filename . ".cache";
        if (!file_exists($filepath) || $this->timestamp > filemtime($filepath)) {
            if ($cache->save($filename, $data, $expire)) {
                Logger::debug("Write template cache file: " . $filepath);
            } else {
                throw new IOException("File write failure: " . $filepath);
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
     * トークンを追加する
     * @param array<string> Viewに描画するパラメータの参照
     * @param string HTML文字列の参照
     */
    private function addToken(&$params, &$content)
    {
        $token = sha1($this->session->id() . microtime());
        $this->session->set($this->getCsrfTokenKey(), $token);
        $params["__csrf_token__"] = $token;
        $this->addToeknHTML($content);
    }

    /**
     * すべてのformタグにCSRF対策トークンを追加する
     * @param string HTML文字列の参照
     */
    private function addToeknHTML(&$content)
    {
        // <meta>タグによるcharsetが指定されない場合は文字化けするのでその対策を行う
        $content = mb_convert_encoding($content, 'html-entities', "UTF-8");
        // DOMでformにアペンドする
        $doc = new \DOMDocument();
        // テンプレートがが断片でなく、完全の場合(layoutを使わずrenderだけで描画した場合)
        // 警告が出るが処理は正常に実行出来るので無視する
        @$doc->loadHTML($content);
        $nodeList = $doc->getElementsByTagName("form");
        $dummy_value = $this->getRandomString();
        $nodeLength = $nodeList->length;
        for ($i = 0; $i < $nodeLength; $i++) {
            $node = $nodeList->item($i);
            $method = $node->getAttribute("method");
            if (preg_match('/^post$|^get$/i', $method)) {
                $newNode = $doc->createElement("input");
                $newNode->setAttribute("type", "hidden");
                $newNode->setAttribute("name", $this->getCsrfTokenKey());
                $newNode->setAttribute("value", $dummy_value);
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
            $content = str_replace($dummy_value, '<?php echo $__csrf_token__; ?>', $innerHTML);
        }
        // 実体参照化をもとに戻す。
        $map = array('&gt;' => '>',
                     '&lt;' => '<',
                     '%20'  => ' ',
                     '%24'  => '$',
                     '%5C'  => '\\');

        $content = str_replace(array_keys($map), array_values($map), $content);
    }
}
