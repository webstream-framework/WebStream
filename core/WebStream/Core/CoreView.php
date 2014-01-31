<?php
namespace WebStream\Core;

use WebStream\Module\Logger;
use WebStream\Module\Cache;
use WebStream\Module\Container;
use WebStream\Module\Utility;
use WebStream\Exception\IOException;
use WebStream\Exception\ResourceNotFoundException;

/**
 * CoreViewクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/12
 * @version 0.4
 */
class CoreView implements CoreInterface
{
    use Utility;

    /** ヘルパのレシーバ名 */
    const HELPER_RECEIVER = "__HELPER__";
    /** リクエスト */
    private $request;
    /** レスポンス */
    private $response;
    /** セッション */
    private $session;
    /** CoreDelegator */
    private $coreDelegator;
    /** テンプレートのタイムスタンプ */
    private $timestamp;
    /** キャッシュ保存ディレクトリ */
    private $cacheDir;

    /**
     * Override
     */
    public function __construct(Container $container)
    {
        Logger::debug("View start.");
        $this->request  = $container->request;
        $this->response = $container->response;
        $this->session  = $container->session;
        $this->coreDelegator = $container->coreDelegator;
        $this->initialize();
    }

    /**
     * Override
     */
    public function __destruct()
    {
        Logger::debug("View end.");
    }

    /**
     * 初期化処理
     */
    private function initialize()
    {
        $this->timestamp = 0;
        $this->cacheDir = STREAM_APP_ROOT . "/app/views/" . STREAM_VIEW_CACHE;
    }

    /**
     * テンプレートキャッシュを作成する
     * @param string テンプレートファイルパス
     * @param string 保存データ
     * @param integer 有効期限
     */
    final public function cache($filename, $data, $expire)
    {
        $cache = new Cache($this->cacheDir);
        $filepath = $this->cacheDir . "/" . $filename . ".cache";
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
     * @param string テンプレートファイルパス
     * @param mixed 埋め込みパラメータ
     */
    final public function draw($template, $params, $mime = "html")
    {
        // テンプレートファイルがない場合エラー
        if (!file_exists($template)) {
            throw new ResourceNotFoundException("Invalid template file path: " . $template);
        }

        // テンプレートファイルの最新の変更日時を取得
        $timestamp = filemtime($template);
        if ($timestamp > $this->timestamp) {
            $this->timestamp = $timestamp;
        }

        // テンプレートが見つからない場合は500になるのでエラー処理は不要
        $content = $this->convert(file_get_contents($template));

        // formタグが含まれる場合はCSRFトークンを付与する
        if (preg_match('/<form.*?>.*?<\/form>/is', $content)) {
            $this->addToken($params, $content);
        } else {
            // formタグがない場合、CSRFトークンセッションは不要なので削除
            $this->session->delete($this->getCsrfTokenKey());
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
        $this->outputHeader($mime);
        $this->outputHTML($temp, $params);

        unlink($temp);
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
     * 共通ヘッダを出力する
     * @param String ファイルタイプ
     */
    private function outputHeader($type)
    {
        $this->response->setType($type);
    }

    /**
     * テンプレートの内容を置換する
     * @param String テンプレートファイルの内容
     * @return String 置換後のテンプレートファイルの内容
     */
    private function convert($s)
    {
        $s = preg_replace('/^<\?xml/', '<<?php ?>?xml', $s);
        $s = preg_replace('/#\{(.*?)\}/', '<?php echo $1; ?>', $s);
        $s = preg_replace('/%\{(.*?)\}/', '<?php echo \WebStream\Module\safetyOut($1); ?>', $s);
        $s = preg_replace('/<%\s(.*?)\s%>/', '<?php $1; ?>', $s);
        $s = preg_replace('/!\{(.*?)\((.*?)\)\}/', '<?php $this->coreDelegator->getHelper()->__initialize("$1", $__params__, [$2]); ?>', $s);
        $s = preg_replace('/@\{(.*?)\}/', '<?php $this->draw(STREAM_APP_ROOT."/app/views/$1", $__params__); ?>', $s);

        return $s;
    }

    /**
     * トークンを追加する
     * @param Hash Viewに描画するパラメータの参照
     * @param String HTML文字列の参照
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
     * @param String HTML文字列の参照
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


    // 以下のメソッドは削除予定


    /**
     * JSONを描画する
     * @param Hash 出力データ
     */
    final public function __json($params)
    {
        $this->outputHeader("json");
        echo json_encode($params);
    }

    /**
     * JSONPを描画する
     * @param Hash 出力データ
     * @param String コールバック関数名
     */
    final public function __jsonp($params, $callback)
    {
        $this->outputHeader("jsonp");
        echo $callback . "(" . json_encode($params) . ");";
    }
}
