<?php
namespace WebStream\Http;

use WebStream\DI\Injector;
use WebStream\Util\CommonUtils;

/**
 * Response
 * @author Ryuichi TANAKA.
 * @since 2012/12/19
 * @version 0.7
 */
class Response
{
    use Injector, CommonUtils;

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
    private $accessControlAllowOrigin = [];
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
     * デストラクタ
     */
    public function __destruct()
    {
        $this->logger->debug("Response is clear.");
    }

    /**
     * 文字コードを設定
     * @param String 文字コード
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Cache-Controlを設定
     * @param String Cache-Control
     */
    public function setCacheControl($cacheControl)
    {
        $this->cacheControl = $cacheControl;
    }

    /**
     * Pragmaを設定
     * @param String Pragma
     */
    public function setPragma($pragma)
    {
        $this->pragma = $pragma;
    }

    /**
     * MimeTypeを設定
     * ファイルタイプにより指定
     * @param String ファイルタイプ
     */
    public function setType($fileType)
    {
        if (array_key_exists($fileType, $this->mime)) {
            $this->mimeType = $this->mime[$fileType];
        } else {
            // 不明なファイルが指定された場合、画面に表示させずダウンロードさせる
            $this->mimeType = $this->mime['file'];
        }
    }

    /**
     * MimeTypeを設定
     * MimeTypeを直接指定
     * @param String MimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    /**
     * リダイレクトロケーションを設定
     * @param String ロケーションパス
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Access-Control-Allow-Originを設定
     * 複数指定する場合は、引数に列挙する
     * @param String URLまたはワイルドカード
     */
    public function setAccessControlAllowOrigin()
    {
        $arguments = func_get_args();
        foreach ($arguments as $argument) {
            $this->accessControlAllowOrigin[] = $argument;
        }
    }

    /**
     * X-Frame-Optionsを設定
     * @param String SAMEORIGINまたはDENY
     */
    public function setXFrameOptions($xframeOptions)
    {
        $this->xframeOptions = $xframeOptions;
    }

    /**
     * X-XSS-Protectionを設定
     * @param String XSSフィルタ設定(0:無効、1:有効)
     */
    public function setXXssProtection($xxssProtection)
    {
        $this->xxssProtection = $xxssProtection;
    }

    /**
     * ステータスコードを設定
     * @param Integer ステータスコード
     */
    public function setStatusCode($statusCode)
    {
        if (!is_string($statusCode) && !is_int($statusCode)) {
            throw new ConnectionException("Invalid status code format: " . strval($statusCode));
        }

        if (!array_key_exists($statusCode, $this->status)) {
            throw new ConnectionException("Unknown status code: " . $statusCode);
        }
        $this->statusCode = $statusCode;
    }

    /**
     * Content-Lengthを設定
     * @param Integer Content-Length
     */
    public function setContentLength($contentLength)
    {
        $this->contentLength = $contentLength;
    }

    /**
     * Content-Dispositionを設定
     * @param String ファイル名
     */
    public function setContentDisposition($filename)
    {
        if (file_exists($filename)) {
            $this->contentDisposition = 'attachement; filename="'. basename($filename) . '"';
        }
    }

    /**
     * Content-Transfer-Encodingを設定
     * @param String エンコーディング方法
     */
    public function setContentTransferEncoding($contentTransferEncoding)
    {
        $this->contentTransferEncoding = $contentTransferEncoding;
    }

    /**
     * Expiresを設定
     * @param Integer 有効期限
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
    }

    /**
     * レスポンスボディを設定
     * @param String レスポンスボディ
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * レスポンスファイルを設定
     * @param String レスポンスファイル
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * レスポンスを送出する
     */
    public function send()
    {
        $this->header();
        $this->body();
    }

    /**
     * レスポンスヘッダを送出する
     */
    public function header()
    {
        if (headers_sent()) {
            return;
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
            $this->contentLength = $this->bytelen($this->body);
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

        $this->logger->info("HTTP access occured: status code " . $this->statusCode);
    }

    /**
     * レスポンスボディを送出する
     */
    public function body()
    {
        if ($this->file !== null) {
            // バイナリ系、その他のファイルはダウンロードする
            ob_clean();
            flush();
            readfile($this->file);
        } else {
            // テキスト系は画面に表示する
            echo $this->body;
        }
    }

    /**
     * Mime-Type
     */
    protected $mime = [
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
        'tsv'   => 'text/tab-separated-values',
        'js'    => 'text/javascript',
        'jsonp' => 'text/javascript',
        'json'  => 'application/json',
        'pdf'   => 'application/pdf',
        'file'  => 'application/octet-stream'
    ];

    /**
     * Status
     */
    protected $status = [
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
    ];

    /**
     * 301 alias
     * @param String redirect url
     */
    public function movePermanently($url)
    {
        $this->setLocation($url);
        $this->setStatusCode(301);
        $this->send();
    }

    /**
     * 400 alias
     */
    public function badRequest()
    {
        $this->move(400);
    }

    /**
     * 401 alias
     */
    public function unauthorized()
    {
        $this->move(401);
    }

    /**
     * 403 alias
     */
    public function forbidden()
    {
        $this->move(403);
    }

    /**
     * 404 alias
     */
    public function notFound()
    {
        $this->move(404);
    }

    /**
     * 405 alias
     */
    public function methodNotAllowed()
    {
        $this->move(405);
    }

    /**
     * 422 alias
     */
    public function unprocessableEntity()
    {
        $this->move(422);
    }

    /**
     * 500 alias
     */
    public function internalServerError()
    {
        $this->move(500);
    }

    /**
     * 静的ファイルを表示
     * @param String ファイル名
     */
    public function displayFile($filename)
    {
        $type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $this->setType($type);
        $this->setContentLength(file_exists($filename) ? filesize($filename) : 0);
        $this->setFile($filename);
    }

    /**
     * ファイルをダウンロード
     * @param String ファイル名
     * @param String ユーザエージェント
     */
    public function downloadFile($filename, $ua)
    {
        $type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $this->setType($type);
        $this->setContentLength(file_exists($filename) ? filesize($filename) : 0);
        $this->setContentDisposition($filename);
        $this->setExpires(0);
        $this->setContentTransferEncoding('binary');
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
    public function move($statusCode)
    {
        if (ob_get_contents()) {
            ob_clean();
        }
        $statusCode = array_key_exists($statusCode, $this->status) ? $statusCode : 500;
        $this->setStatusCode($statusCode);
        $bodyMessage = $statusCode . ' ' . $this->status[$statusCode];
        $this->setBody($this->bodyTemplate($bodyMessage));
        $this->send();
        exit;
    }

    /**
     * レスポンス送出を開始する
     */
    public function start()
    {
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * レスポンスを送出して終了する
     */
    public function end()
    {
        $body = "";
        if (($error = error_get_last()) === null) {
            $body = ob_get_clean();
        } else {
            switch ($error['type']) {
                case E_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                case E_RECOVERABLE_ERROR:
                case E_PARSE:
                    $this->clean();
                    break;
                case E_WARNING:
                case E_CORE_WARNING:
                case E_COMPILE_WARNING:
                case E_USER_WARNING:
                case E_STRICT:
                case E_NOTICE:
                case E_USER_NOTICE:
                case E_DEPRECATED:
                case E_USER_DEPRECATED:
                    $body = ob_get_clean();
                    break;
            }
        }

        $this->setBody($body);
        $this->send();
    }

    /**
     * レスポンス送出せず終了する
     */
    public function clean()
    {
        ob_end_clean();
    }

    /**
     * HTMLテンプレート
     * @param String 表示内容
     * @return String HTML
     */
    private function bodyTemplate($content)
    {
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
