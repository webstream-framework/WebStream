<?php
namespace WebStream;

/**
 * レスポンス基底クラス
 * @author Ryuichi TANAKA.
 * @since 2012/12/19
 */
class ResponseBase {
    /** HTTPバージョン */
    const HTTP_VERSION = '1.1';
    /** 文字コード */
    private $charset = 'UTF-8';
    /** Cache-Control */
    private $cacheControl = 'no-cache';
    /** Pragma */
    private $pragma = 'no-cache';
    /** Mime type */
    private $mimeType = 'text/html';
    /** ロケーション */
    private $location;
    /** Access-Control-Allow-Origin */
    private $accessControlAllowOrigin = array();
    /** X-Frame-Options */
    private $xframeOptions = 'SAMEORIGIN';
    /** X-XSS-Protection */
    private $xxssProtection = '1; mode=block';
    /** ステータスコード */
    private $statusCode = 200;
    /** レスポンスボディ */
    private $body;
    /** レスポンスファイル */
    private $file;
    /** Content-Length */
    private $contentLength;
    /** Content-Disposition */
    private $contentDisposition;
    /** Content-Transfer-Encoding */
    private $contentTransferEncoding;
    /** Expires */
    private $expires;

    /**
     * コンストラクタ
     */
    private function __construct() {}

    /**
     * 文字コードを設定
     * @param String 文字コード
     */
    public function setCharset($charset) {
        $this->charset = $charset;
    }

    /**
     * Cache-Controlを設定
     * @param String Cache-Control
     */
    public function setCacheControl($cacheControl) {
        $this->cacheControl = $cacheControl;
    }

    /**
     * Pragmaを設定
     * @param String Pragma
     */
    public function setPragma($pragma) {
        $this->pragma = $pragma;
    }

    /**
     * MimeTypeを設定
     * ファイルタイプにより指定
     * @param String ファイルタイプ
     */
    public function setType($fileType) {
        $this->mimeType = $this->mime[$fileType];
        // 不明なファイルが指定された場合、画面に表示させずダウンロードさせる
        if (!$this->mimeType) {
            $this->mimeType = $this->mime['file'];
        }
    }

    /**
     * MimeTypeを設定
     * MimeTypeを直接指定
     * @param String MimeType    
     */
    public function setMimeType($mimeType) {
        $this->mimeType = $mimeType;
    }

    /**
     * リダイレクトロケーションを設定
     * @param String ロケーションパス
     */
    public function setLocation($location) {
        $this->location = $location;
    }

    /**
     * Access-Control-Allow-Originを設定
     * 複数指定する場合は、引数に列挙する
     * @param String URLまたはワイルドカード
     */
    public function setAccessControlAllowOrigin() {
        $arguments = func_get_args();
        foreach ($arguments as $argument) {
            $this->accessControlAllowOrigin[] = $argument;
        }
    }

    /**
     * X-Frame-Optionsを設定
     * @param String SAMEORIGINまたはDENY
     */
    public function setXFrameOptions($xframeOptions) {
        $this->xframeOptions = $xframeOptions;
    }

    /**
     * X-XSS-Protectionを設定
     * @param String XSSフィルタ設定(0:無効、1:有効)
     */
    public function setXXssProtection($xxssProtection) {
        $this->xxssProtection = $xxssProtection;
    }

    /**
     * ステータスコードを設定
     * @param Integer ステータスコード    
     */
    public function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
    }

    /**
     * Content-Lengthを設定
     * @param Integer Content-Length
     */
    public function setContentLength($contentLength) {
        $this->contentLength = $contentLength;
    }

    /**
     * Content-Dispositionを設定
     * @param String ファイル名
     */
    public function setContentDisposition($filename) {
        if (file_exists($filename)) {
            $this->contentDisposition = 'attachement; filename="'. basename($filename) . '"';
        }
    }

    /**
     * Content-Transfer-Encodingを設定
     * @param String エンコーディング方法
     */
    public function setContentTransferEncoding($contentTransferEncoding) {
        $this->contentTransferEncoding = $contentTransferEncoding;
    }

    /**
     * Expiresを設定
     * @param Integer 有効期限
     */
    public function setExpires($expires) {
        $this->expires = $expires;
    }

    /**
     * レスポンスボディを設定
     * @param String レスポンスボディ
     */
    public function setBody($body) {
        $this->body = $body;
    }

    /**
     * レスポンスファイルを設定
     * @param String レスポンスファイル
     */
    public function setFile($file) {
        $this->file = $file;
    }

    /**
     * レスポンスを送出する
     */
    public function send() {
        $this->header();
        $this->body();
    }

    /**
     * レスポンスヘッダを送出する
     */
    public function header() {
        if (!array_key_exists($this->statusCode, $this->status)) {
            throw new ConnectionException("Unknown status code: " . $this->statusCode);
        }

        // StatusCode
        $headerMessage = 'HTTP/' . self::HTTP_VERSION . ' ' .
                         $this->statusCode . ' ' . $this->status[$this->statusCode];
        header($headerMessage);


        // Redirect
        if (intval($this->statusCode) === 301) {
            header('Location: ' . $this->location);
        }

        // Content-Type
        header('Content-Type: ' . $this->mimeType . '; charset=' . $this->charset);

        // Content-Length
        if ($this->contentLength === null) {
            $this->contentLength = Utility::bytelen($this->body);
        }
        header('Content-Length: ' . $this->contentLength);

        // Content-Disposition
        if ($this->contentDisposition !== null) {
            header('Content-Disposition: ' . $this->contentDisposition);
        }

        // Content-Transfer-Encoding
        if ($this->contentTransferEncoding !== null) {
            header('Content-Transfer-Encoding: ' . $this->contentTransferEncoding);
        }

        // Cache-Control
        header('Cache-Control: ' . $this->cacheControl);

        // Pragma
        header('Pragma: ' . $this->pragma);

        // Expires
        if ($this->expires !== null) {
            header('Expires: ' . $this->expires);
        }

        // X-Content-Type-Options
        header("X-Content-Type-Options: nosniff");

        // Access-Control-Allow-Origin
        if (!empty($this->accessControlAllowOrigin)) {
            header('Access-Control-Allow-Origin: ' . implode(',', $this->accessControlAllowOrigin));
        }

        // X-Frame-Options
        if ($this->xframeOptions !== null) {
            header('X-Frame-Options: ' . $this->xframeOptions);
        }

        // X-XSS-Protection
        if ($this->xxssProtection !== null) {
            header('X-XSS-Protection: ' . $this->xxssProtection);
        }

        Logger::info("HTTP access occured: status code " . $this->statusCode);
    }

    /**
     * レスポンスボディを送出する
     */
    public function body() {
        $type = array_search($this->mimeType, $this->mime);
        // テキスト系は画面に表示する
        if (in_array($type, $this->textType)) {
            echo $this->body;
        }
        // バイナリ系、その他のファイルはダウンロードする
        else {
            ob_clean();
            flush();
            readfile($this->file);
        }
    }

    /**
     * Mime-Type
     */
    private $mime = array(
        'txt'   => 'text/plain',
        'jpeg'  => 'image/jpeg',
        'jpg'   => 'image/jpeg',
        'gif'   => 'image/gif',
        'png'   => 'image/png',
        'tiff'  => 'image/tiff',
        'tif'   => 'image/tiff',
        'bmp'   => 'image/bmp',
        'ico'   => 'image/x-icon',
        'svg'   => 'image/svg+xml',
        'xml'   => 'application/xml',
        'xsl'   => 'application/xml',
        'rss'   => 'application/rss+xml',
        'rdf'   => 'application/rdf+xml',
        'atom'  => 'application/atom+xml',
        'zip'   => 'application/zip',
        'html'  => 'text/html',
        'htm'   => 'text/html',
        'css'   => 'text/css',
        'csv'   => 'text/csv',
        'js'    => 'text/javascript',
        'jsonp' => 'text/javascript',
        'json'  => 'application/json',
        'pdf'   => 'application/pdf',
        'file'  => 'application/octet-stream'
    );

    /**
     * Mime-Type(text)
     */
    private $textType = array(
        'txt','svg','xml','xsl','rss',
        'rdf','atom','html','htm','css',
        'csv','js','jsonp'
    );

    /**
     * Status
     */
    protected $status = array(
        '100' => 'Continue',
        '101' => 'Switching Protocols',
        '102' => 'Processing',
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',
        '207' => 'Multi-Status',
        '208' => 'Already Reported',
        '226' => 'IM Used',
        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '307' => 'Temporary Redirect',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Request Entity Too Large',
        '414' => 'Request-URI Too Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Requested Range Not Satisfiable',
        '417' => 'Expectation Failed',
        '418' => "I'm a teapot",
        '422' => 'Unprocessable Entity',
        '423' => 'Locked',
        '424' => 'Failed Dependency',
        '426' => 'Upgrade Required',
        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Timeout',
        '505' => 'HTTP Version Not Supported',
        '506' => 'Variant Also Negotiates',
        '507' => 'Insufficient Storage',
        '508' => 'Loop Detected',
        '510' => 'Not Extended',
    );
}

/**
 * レスポンスクラス
 * @author Ryuichi TANAKA.
 * @since 2012/12/19
 */
class Response extends ResponseBase {
	/** レスポンスオブジェクト */
	private static $response = null;

    /**
     * インスタンス生成を禁止
     */
	private function __construct() {}

    /**
     * レスポンスオブジェクトを返却する
     * @param Object レスポンスオブジェクト
     */
	public static function getInstance() {
		if (!is_object(self::$response)) {
			self::$response = new Response();
		}
		return self::$response;
	}

    /**
     * 301 alias
     * @param String redirect url
     */
    public function movePermanently($url) {
        $this->setLocation($url);
        $this->setStatusCode(301);
        $this->send();
    }

    /**
     * 400 alias
     */
    public function badRequest() {
        $this->move(400);
    }

    /**
     * 401 alias
     */
    public function unauthorized() {
        $this->move(401);
    }

    /**
     * 403 alias
     */
    public function forbidden() {
        $this->move(403);
    }

    /**
     * 404 alias
     */
    public function notFound() {
        $this->move(404);
    }

    /**
     * 405 alias
     */
    public function methodNotAllowed() {
        $this->move(405);
    }

    /**
     * 422 alias
     */
    public function unprocessableEntity() {
        $this->move(422);
    }

    /**
     * 500 alias
     */
    public function internalServerError() {
        $this->move(500);
    }

    /**
     * 静的ファイルを表示
     * @param String ファイル名
     */
    public function displayFile($filename) {
        $type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $this->setType($type);
        $this->setContentLength(filesize($filename));
        $this->setFile($filename);
    }

    /**
     * ファイルをダウンロード
     * @param String ファイル名
     */
    public function downloadFile($filename) {
        $request = Request::getInstance();
        $type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $this->setType($type);
        $this->setContentLength(filesize($filename));
        $this->setContentDisposition($filename);
        $this->setExpires(0);
        $this->setContentTransferEncoding('binary');
        $ua = $request->userAgent();
        if (isset($ua) && strpos($ua, 'MSIE')) {
            $this->setCacheControl('must-revalidate, post-check=0, pre-check=0');
            $this->setPragma('public');
        }
        $this->setFile($filename);
    }

    /**
     * 指定したステータスコードのページに遷移
     * @param Integer ステータスコード
     */
    public function move($statusCode) {
        $this->setStatusCode($statusCode);
        $bodyMessage = $statusCode . ' ' . $this->status[$statusCode];
        $this->setBody($this->bodyTemplate($bodyMessage));
        $this->send();
        exit;
    }

    /**
     * HTMLテンプレート
     * @param String 表示内容
     * @return String HTML
     */
    private function bodyTemplate($content) {
        return <<< HTML
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
}