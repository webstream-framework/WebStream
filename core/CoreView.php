<?php
namespace WebStream;
/**
 * Coreクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/12
 */
class CoreView {
    /** ヘルパのレシーバ名 */
    const HELPER_RECEIVER = "helper";
    /** ページ名 */
    private $page_name;
    /** セッション */
    private $session;
    /** テンプレートリスト */
    private $templates;
    /** CSRF対策 */
    public $enableCsrf = false;

    /**
     * Viewクラスの初期化
     * @param String ページ名
     */
    public function __construct($page_name = null) {
        $this->page_name = $page_name;
    }

    /**
     * テンプレートリスト情報を設定
     * @param Hash テンプレートリスト情報
     */
    public function __templates($templates) {
        $this->templates = $templates;
    }

    /**
     * レイアウトファイルを描画する準備をする
     * @param String テンプレートファイル名
     * @param Hash 埋め込みパラメータ
     * @param String ファイルタイプ
     */
    final public function layout($template, $params = array(), $type = "html") {
        $template_path = STREAM_ROOT . "/" . STREAM_APP_DIR . "/views" .
                         "/" . STREAM_VIEW_SHARED . "/" . $template;
        $this->draw($template_path, $params, $type);
    }
    
    /**
     * テンプレートファイルを描画する準備をする
     * @param String テンプレートファイル名
     * @param Hash 埋め込みパラメータ
     * @param String ファイルタイプ
     */
    final public function render($template, $params = array(), $type = "html") {
        $template_path = STREAM_ROOT . "/" . STREAM_APP_DIR . 
                         "/views/" . Utility::camel2snake($this->page_name) . "/" . $template;
        $this->draw($template_path, $params, $type);
    }

    /**
     * JSONを描画する
     * @param Hash 出力データ
     */
    final public function json($params) {
        $this->outputHeader("json");
        echo json_encode($params);
    }
    
    /**
     * JSONPを描画する
     * @param Hash 出力データ
     * @param String コールバック関数名
     */
    final public function jsonp($params, $callback) {
        $this->outputHeader("jsonp");
        echo $callback . "(" . json_encode($params) . ");";
    }
    
    /**
     * デフォルトHTMLを出力する
     * @param String エラー内容
     */
    final public function error($content) {
        echo <<< HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <head>
        <title>$content</title>
    </head>
    <body>
        <h1>$content</h1>
    </body>
</html>
HTML;
    }
    
    /**
     * テンプレートファイルを描画する
     * @param String テンプレートファイルパス
     * @param Hash 埋め込みパラメータ
     * @param String ファイルタイプ
     */
    final private function draw($template_path, $params, $type) {
        // セッションを取得
        $this->session = Session::start();

        // テンプレートファイルがない場合エラー
        if (!file_exists(realpath($template_path))) {
            throw new TemplateNotFoundException("Invalid template file path: " . $template_path);
        }
        
        // 埋め込みパラメータにHelperを起動するためのオブジェクトをセット
        $params[self::HELPER_RECEIVER] = new CoreHelper($this->page_name);

        // キャッシュファイルがなければ生成する
        $filename = preg_replace_callback('/.*views\/(.*)\.tmpl$/', function($matches) {
            return $matches[1] . '.cache';
        }, $template_path);
        $cache_file = STREAM_ROOT . '/' . STREAM_APP_DIR . '/views/' . STREAM_VIEW_CACHE . "/" . md5($filename);

        // テンプレートが見つからない場合は500になるのでエラー処理は不要
        $content = $this->convert(file_get_contents($template_path));

        // formタグが含まれる場合はCSRFトークンを付与する
        if ($this->enableCsrf && preg_match('/<form.*?>.*?<\/form>/is', $content)) {
            $this->addToken($params, $content);
        }
        // formタグがない場合、CSRFトークンセッションは不要なので削除
        else {
            $this->session->delete(Utility::getCsrfTokenKey());
        }

        // テンプレートに書き出す
        if (!file_exists($cache_file) || filemtime($cache_file) < filemtime($template_path)) {
            file_put_contents($cache_file, $content, LOCK_EX);
        }

        // 入れ子のテンプレートにパラメータをセットする
        $params["__params__"] = $params;
        $params["__templates__"] = $this->templates;

        $this->outputHeader($type);
        $this->outputHTML($params, $cache_file);
    }

    /**
     * テンプレートを描画する
     * @param Hash 展開するパラメータ
     * @param String テンプレートファイル
     */
    final private function outputHTML($__params__, $__template__) {
        extract($__params__);
        include($__template__);
    }
    
    /**
     * CSRF対策を有効にする
     */
    final public function enableCsrf() {
        $this->enableCsrf = true;
    }
    
    /**
     * トークンを追加する
     * @param Hash Viewに描画するパラメータの参照
     * @param String HTML文字列の参照
     */
    final private function addToken(&$params, &$content) {
        // CSRFの設定が有効の場合
        $security = Utility::parseConfig("config/security.ini");
        if (intval($security["csrf_check"]) === 1) {
            $token = sha1($this->session->id() . microtime());
            $this->session->set(Utility::getCsrfTokenKey(), $token);
            $params["__csrf_token__"] = $token;
            $this->addToeknHTML($content);
        }
    }
    
    /**
     * すべてのformタグにCSRF対策トークンを追加する
     * @param String HTML文字列の参照
     */    
    final private function addToeknHTML(&$content) {
        // <meta>タグによるcharsetが指定されない場合は文字化けするのでその対策を行う
        $content = mb_convert_encoding($content, 'html-entities', "UTF-8"); 
        // DOMでformにアペンドする
        $doc = new \DOMDocument();
        // テンプレートがが断片でなく、完全の場合(layoutを使わずrenderだけで描画した場合)
        // 警告が出るが処理は正常に実行出来るので無視する
        @$doc->loadHTML($content);
        $nodeList = $doc->getElementsByTagName("form");
        $dummy_value = Utility::getRandomString();
        $nodeLength = $nodeList->length;
        for ($i = 0; $i < $nodeLength; $i++) {
            $node = $nodeList->item($i);
            $method = $node->getAttribute("method");
            if (preg_match('/^post$|^get$/i', $method)) {
                $newNode = $doc->createElement("input");
                $newNode->setAttribute("type", "hidden");
                $newNode->setAttribute("name", Utility::getCsrfTokenKey());
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
    final public function renderPublicFile($filepath) {
        if (!file_exists($filepath)) {
            throw new ResourceNotFoundException("File not found: " . $filepath);
        }
        // 画像,css,jsの場合
        if (preg_match('/\/views\/'.STREAM_VIEW_PUBLIC.'\/img\/.+\.(?:jp(?:e|)g|png|bmp|(?:tif|gi)f)$/i', $filepath) ||
            preg_match('/\/views\/'.STREAM_VIEW_PUBLIC.'\/css\/.+\.css$/i', $filepath) ||
            preg_match('/\/views\/'.STREAM_VIEW_PUBLIC.'\/js\/.+\.js$/i', $filepath)) {
            $this->display($filepath);
        }
        // それ以外のファイル
        else if (preg_match('/\/views\/'.STREAM_VIEW_PUBLIC.'\/file\/.+$/i', $filepath)) {
            $this->download($filepath);
        }
    }
    
    /**
     * テンプレートの内容を置換する
     * @param String テンプレートファイルの内容
     * @param String 置換後のテンプレートファイルの内容
     */
    final private function convert($s) {
        $s = preg_replace('/^<\?xml/', '<<?php ?>?xml', $s);
        $s = preg_replace('/#\{(.*?)\}/', '<?php echo $1; ?>', $s);
        $s = preg_replace('/%\{(.*?)\}/', '<?php echo \WebStream\safetyOut($1); ?>', $s);
        $s = preg_replace('/<%\s(.*?)\s%>/', '<?php $1; ?>', $s);
        $s = preg_replace('/!\{(.*?)\}/', '<?php ${self::HELPER_RECEIVER}->$1; ?>', $s);
        $s = preg_replace('/@\{(.*?)\}/', '<?php $this->render($__templates__["$1"], $__params__); ?>', $s);
        return $s;
    }
    
    /**
     * 共通ヘッダを出力する
     * @param String ファイルタイプ
     */
    final private function outputHeader($type) {
        $mime = Utility::getMimeType($type);
        header("Content-Type: ${mime}; charset=UTF-8");
        header("X-Content-Type-Options: nosniff");
    }
    
    /**
     * 画像、CSS、JavaScriptファイルを表示する
     * @param String ファイルパス
     */
    final private function display($filename) {
        $type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $this->outputHeader($type);
        header("Content-Length: " . filesize($filename));
        ob_clean();
        flush();
        readfile($filename);
    }
    
    /**
     * ファイルをダウンロードする
     * @param String ファイルパス
     */
    final private function download($filename) {
        $type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $this->outputHeader($type);
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Expires: 0');
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($filename));
        header('Pragma: no-cache');
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE) {
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        }
        ob_clean();
        flush();
        readfile($filename);
    }
}
