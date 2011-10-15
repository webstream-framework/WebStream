<?php
/**
 * Coreクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/12
 */
class CoreView {
    /** appディレクトリ */
    private $app_dir;
    /** ページ名 */
    private $page_name;
    /** セッション */
    private $session;
    /** HTTPヘッダ */
    private $mime;

    /**
     * Viewクラスの初期化
     * @param String appディレクトリパス
     * @param String ページ名
     */
    public function __construct($app_dir = null, $page_name = null) {
        $this->app_dir = $app_dir;
        $this->page_name = $page_name;
        $this->session = Session::start();
    }
    
    /**
     * レイアウトファイルを描画する準備をする
     * @param String テンプレートファイル名
     * @param Hash 埋め込みパラメータ
     */
    final public function layout($template, $params = array()) {
        $template_path = STREAM_ROOT . "/" . $this->app_dir . 
                         "/views/shared/" . $template . ".tmpl";
        $this->draw($template_path, 'shared', $params);
    }
    
    /**
     * テンプレートファイルを描画する準備をする
     * @param String テンプレートファイル名
     * @param Hash 埋め込みパラメータ
     * @param String Mimeタイプ
     */
    final public function render($template, $params = array(), $mime = "html") {
        $template_path = STREAM_ROOT . "/" . $this->app_dir . 
                         "/views/" . $this->page_name . "/" . $template . ".tmpl";
        $this->setMime($mime);
        $this->draw($template_path, $this->page_name, $params);
    }
    
    /**
     * JSONを描画する
     * @param Hash 出力データ
     * @param String コールバック関数名
     */
    final public function json($params, $callback = null) {
        $this->setMime("json");
        $this->outputHeader();
        echo json_encode($params);
    }
    
    /**
     * デフォルト画面を描画する
     * @param int ステータスコード
     * @param String 遷移パス
     */
    final public function move($status_code, $path = null) {
        switch ($status_code) {
        case 301:
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . $path);
            break;
        case 400:
            header("HTTP/1.1 400 Bad Request");
            $this->errorHTML("400 Bad Request");
            break;
        case 404:
            header("HTTP/1.1 404 Not Found");
            $this->errorHTML("404 Not Found");
            break;
        case 500:
            header("HTTP/1.1 500 Internal Server Error");
            $this->errorHTML("500 Internal Server Error");
            break;
        default:
            $msg = "Unknown status code: " . $status_code;
            Logger::error($msg);
            throw new Exception($msg);
        }
        exit;
    }
    
    /**
     * デフォルトHTMLを出力する
     * @param String エラー内容
     */
    final private function errorHTML($content) {
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
     * @param String テンプレートディレクトリ名
     * @param Hash 埋め込みパラメータ
     */
    final private function draw($template_path, $template_name, $params) {
        if (!file_exists(realpath($template_path))) {
            throw new Exception("Invalid template file path: " . $template_path);
        }
        $pathinfo = pathinfo($template_path);

        // キャッシュファイルがなければ生成する
        $filename = str_replace("/", ".", $pathinfo["filename"]) . ".cache";
        $cache_file = STREAM_ROOT . '/' . $this->app_dir . '/views/cache/' . $template_name . '.' . $filename;
        
        // テンプレートが見つからない場合は500になるのでエラー処理は不要
        $content = $this->convert(file_get_contents($template_path));
        
        // formタグが含まれる場合はCSRFトークンを付与する
        if (preg_match('/<form.*?>.*?<\/form>/is', $content)) {
            $this->addToken($params, $content);
        }
        // formタグがない場合、CSRFトークンセッションは不要なので削除
        else {
            $this->session->delete(Utility::getCsrfTokenKey());
        }
        
        // テンプレートに書き出す
        if (!file_exists($cache_file) || filemtime($cache_file) < filemtime($template_path)) {
            file_put_contents($cache_file, $content);
        }
        
        $this->outputHeader();
        extract($params);
        include($cache_file);
    }
    
    /**
     * リダイレクトする
     * @param String リダイレクト先パス
     */
    final public function redirect($path) {
        $this->move(301, $path);
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
        // DOMでformにアペンドする
        $doc = new DOMDocument();
        // テンプレートがが断片でなく、完全の場合(layoutを使わずrenderだけで描画した場合)
        // 警告が出るが処理は正常に実行出来るので無視する
        @$doc->loadHTML($content);
        $nodeList = $doc->getElementsByTagName("form");
        $dummy_value = Utility::getRandomString();
        $nodeLength = $nodeList->length;
        for ($i = 0; $i < $nodeLength; $i++) {
            $node = $nodeList->item($i);
            // methodがPOSTの場合のみ実行する
            $method = $node->getAttribute("method");
            if (preg_match('/^post$/i', $method)) {
                $newNode = $doc->createElement("input");
                $newNode->setAttribute("type", "hidden");
                $newNode->setAttribute("name", Utility::getCsrfTokenKey());
                $newNode->setAttribute("value", $dummy_value);
                $node->appendChild($newNode);
            }
        }
        if ($nodeLength !== 0) {
            $innerHTML = "";
            $bodyNodeList = $doc->getElementsByTagName("body");
            $bodyNode = $bodyNodeList->item(0);
            $children = $bodyNode->childNodes;
            foreach ($children as $child) {
                $tmp = new DOMDocument();
                $tmp->appendChild($tmp->importNode($child, true));
                $innerHTML .= trim($tmp->saveHTML());
            }
            $content = str_replace($dummy_value, '<?php echo $__csrf_token__; ?>', $innerHTML);
        }
    }

    /**
     * publicディレクトリにある静的ファイルを表示する
     * @param String ファイルパス
     */
    final public function renderPublicFile($filepath) {
        if (!file_exists($filepath)) {
            throw new ResoureceNotFoundException("File not found: " . $filepath);
        }
        // 画像の場合
        if (preg_match('/.+\.((?:jp(?:e|)g|png|bmp|(?:tif|gi)f)|(?:JP(?:E|)G|PNG|BMP|(?:TIF|GI)F))$/', 
            $filepath, $matches)) {
            $ext = $matches[1];
            $size = filesize($filepath);
            $handle = fopen($filepath, "rb");
            $content = fread($handle, $size);
            fclose($handle);
            header("Content-type: image/" . $ext);
            header("Content-Length: " . $size);
            echo $content;
        }
        // cssの場合
        else if (preg_match('/.+\.css$/', $filepath)) {
            $size = filesize($filepath);
            $handle = fopen($filepath, "r");
            $content = fread($handle, $size);
            fclose($handle);
            header("Content-type: text/css");
            header("Content-Length: " . $size);
            echo $content;
        }
        // jsの場合
        else if (preg_match('/.+\.js$/', $filepath)) {
            $size = filesize($filepath);
            $handle = fopen($filepath, "r");
            $content = fread($handle, $size);
            fclose($handle);
            header("Content-type: text/javascript");
            header("Content-Length: " . $size);
            echo $content;
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
        $s = preg_replace('/%\{(.*?)\}/', '<?php echo safetyOut($1); ?>', $s);
        $s = preg_replace('/<%\s(.*?)\s%>/', '<?php $1; ?>', $s);
        return $s;
    }
    
    /**
     * MIMEタイプを設定する
     * @param String ファイルタイプ
     */
    final private function setMime($type = null) {
        switch ($type) {
        case "rss":
        case "atom":
        case "xml":
            $this->mime = "application/xml";
            break;
        case "json":
            $this->mime = "application/json";
            break;
        default:
            $this->mime = "text/html";
            break;
        }
    }
    
    /**
     * ヘッダを出力する
     */
    final private function outputHeader() {
        if (!$this->mime) {
            $this->setMime();
        }
        header("Content-Type: {$this->mime}; charset=UTF-8");
        header("X-Content-Type-Options: nosniff");
    }
}
