<?php
namespace WebStream\Module;

use WebStream\Exception\SystemException;

/**
 * Utility
 * @author Ryuichi Tanaka
 * @since 2013/09/06
 * @version 0.4
 */
trait Utility
{
    /**
     * CSRFトークンキーを返却する
     * @return string CSRFトークンキー
     */
    public function getCsrfTokenKey()
    {
        return "__CSRF_TOKEN__";
    }

    /**
     * CoreHelper#asyncで使用するIDを返却する
     * @return string DOMID
     */
    public function getAsyncDomId()
    {
        return $this->getRandomstring(32);
    }

    /**
     * Viewで有効なModel変数名を返却する
     * @return string Model変数名
     */
    public function getModelVariableName()
    {
        return "model";
    }

    /**
     * Viewで有効なHelper変数名を返却する
     * @return string Helper変数名
     */
    public function getHelperVariableName()
    {
        return "helper";
    }

    /**
     * プロジェクトルートファイル名を返却する
     * @return string プロジェクトルートファイル名
     */
    private function getProjectFileName()
    {
        return ".projectroot";
    }

    /**
     * プロジェクトディレクトリの絶対パスを返す
     * @return string プロジェクトディレクトリの絶対パス
     */
    public function getRoot()
    {
        // 上位階層を辿り、.projectrootファイルを見つける
        $targetPath = realpath(dirname(__FILE__));
        $isProjectRoot = false;

        while (!$isProjectRoot) {
            if (file_exists($targetPath . DIRECTORY_SEPARATOR . $this->getProjectFileName())) {
                $isProjectRoot = true;
            } else {
                if (preg_match("/(.*)\//", $targetPath, $matches)) {
                    $targetPath = $matches[1];
                    if (!is_dir($targetPath)) {
                        break;
                    }
                }
            }
        }

        if (!$isProjectRoot) {
            throw new SystemException("'.projectroot' file must be put in directly under the project directory.");
        }

        return $targetPath;
    }

    /**
     * テスト環境でのアプリケーションルートパスを返却する(本番では使用しない)
     * @return string アプリケーションルートパス
     */
    public function getTestApplicationRoot()
    {
        return $this->getRoot() . "/core/WebStream/Test/Sample";
    }

    /**
     * テスト環境でのアプリケーションディレクトリパスを返却する(本番では使用しない)
     * @return string アプリケーションルートパス
     */
    public function getTestApplicationDir()
    {
        return "core/WebStream/Test/Sample/app";
    }

    /**
     * ファイル検索イテレータを返却する
     * @param string ディレクトリパス
     * @return object イテレータ
     */
    public function getFileSearchIterator($path)
    {
        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::LEAVES_ONLY,
            \RecursiveIteratorIterator::CATCH_GET_CHILD // for Permission deny
        );
    }

    /**
     * 指定したファイルの名前空間を取得する
     * @param string ファイルパス
     * @param string 起点ディレクトリパス
     * @return string 名前空間
     */
    public function getNamespace($filepath, $baseDir = null)
    {
        if (file_exists($filepath)) {
            $resource = fopen($filepath, "r");
            while (false !== ($line = fgets($resource))) {
                if (preg_match("/^namespace\s(.*);$/", $line, $matches)) {
                    $namespace = $matches[1];
                    if (substr($namespace, 0) !== '\\') {
                        $namespace = '\\' . $namespace;
                    }

                    return $namespace;
                }
            }
            fclose($resource);
        }

        return null;
    }

    /**
     * 設定ファイルをパースする
     * @param string プロジェクトルートからの相対パス
     * @return hash 設定情報
     */
    public function parseConfig($filepath)
    {
        // 正規化した絶対パス
        $realpath = $this->getRoot() . DIRECTORY_SEPARATOR . $filepath;

        return file_exists($realpath) ? parse_ini_file($realpath) : null;
    }

    /**
     * ランダムな文字列を生成して返却する
     * @param int 生成する文字数(省略時は10文字)
     * @return string ランダム文字列
     */
    public function getRandomstring($length = 10)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
        mt_srand();
        $random_str = "";
        for ($i = 0; $i < $length; $i++) {
            $random_str .= $chars{mt_rand(0, strlen($chars) - 1)};
        }

        return $random_str;
    }

    /**
     * 一時ディレクトリパスを返却する
     * @return string 一時ディレクトリパス
     */
    public function getTemporaryDirectory()
    {
        return PHP_OS === "WIN32" || PHP_OS === "WINNT" ? "C:\\Windows\\Temp" : "/tmp";
    }

    /**
     * キャメルケース文字列をスネークケース文字列に置換する
     * @param string キャメルケース文字列
     * @return string スネークケース文字列
     */
    public function camel2snake($str)
    {
        $str = preg_replace_callback('/([A-Z])/', function ($matches) {
            return '_' . lcfirst($matches[1]);
        }, $str);

        return preg_replace('/^_/', '', $str);
    }

    /**
     * スネークケース文字列をアッパーキャメルケースに置換する
     * @param string スネークケース文字列
     * @return string アッパーキャメルケース文字列
     */
    public function snake2ucamel($str)
    {
        $str = ucfirst(preg_replace_callback('/_([a-zA-Z])/', function ($matches) {
            return ucfirst($matches[1]);
        }, $str));

        return $str;
    }

    /**
     * スネークケース文字列をローワーキャメルケースに置換する
     * @param string スネークケース文字列
     * @return string ローワーキャメルケース文字列
     */
    public function snake2lcamel($str)
    {
        return lcfirst(self::snake2ucamel($str));
    }

    /**
     * XMLオブジェクトを配列に変換する
     * @param object XMLオブジェクト
     * @return Hash 配列/ハッシュデータ
     */
    public function xml2array($xml)
    {
        $result = array();
        if (is_object($xml)) {
            $list = get_object_vars($xml);
            while (list($k, $v) = each($list)) {
                $result[$k] = Utility::xml2array($v);
            }
        } elseif (is_array($xml)) {
            while (list($k, $v) = each($xml)) {
                $result[$k] = Utility::xml2array($v);
            }
        } else {
            $result = $xml;
        }

        return $result;
    }

    /**
     * ファイルからmimeタイプを返却する
     * @param string ファイルタイプ
     * @return string mimeタイプ
     */
    public function getMimeType($type)
    {
        switch ($type) {
            case "txt":
                return "text/plain";
            case "jpeg":
            case "jpg":
                return "image/jpeg";
            case "gif":
                return "image/gif";
            case "png":
                return "image/png";
            case "tiff":
                return "image/tiff";
            case "bmp":
                return "image/bmp";
            case "xml":
            case "rss":
            case "rdf":
            case "atom":
                 return "application/xml";
            case "html":
            case "htm":
                return "text/html";
            case "css":
                return "text/css";
            case "js":
            case "jsonp":
                return "text/javascript";
            case "json":
                return "application/json";
            case "pdf":
                return "application/pdf";
            default:
                return "application/octet-stream";
        }
    }

    /**
     * データのバイト長を返却する
     * @param string 文字列
     * @return string バイト長
     */
    public function bytelen($data)
    {
        return strlen(bin2hex($data)) / 2;
    }

    /**
     * データをシリアライズ化してテキストデータにエンコードする
     * @param object 対象データ
     * @return string エンコードしたデータ
     */
    public function encode($data)
    {
        return base64_encode(serialize($data));
    }

    /**
     * データをデシリアライズ化して元のデータをデコードする
     * @param string エンコード済みデータ
     * @return object デコードしたデータ
     */
    public function decode($data)
    {
        return unserialize(base64_decode($data));
    }

    /**
     * 要素が存在するかどうか
     * @param array 検索対象配列
     * @param mixed 検索値
     * @return bool 存在すればtrue
     */
    public function inArray($target, $list)
    {
        $type = gettype($target);
        switch ($type) {
            case "string":
            case "integer":
                return array_key_exists($target, array_flip($list));
            default:
                // それ以外の場合、in_arrayを使用する
                return in_array($target, $list, true);
        }
    }

    /**
     * CoreHelper#asyncで使用するコードを返却する
     * @param string URL
     * @param string CSSクラス名
     * @return string コード
     */
    public function asyncHelperCode($url, $id)
    {
        return <<< JSCODE
(function (c,b) {var a;a=window.XMLHttpRequest?new XMLHttpRequest:new ActiveXObject("Microsoft.XMLHTTP");a.onreadystatechange=function () {4==a.readyState&&200==a.status&&(document.getElementById(b).outerHTML=a.responseText)};a.open("GET",c,!0);a.send()})("$url","$id");
JSCODE;
    }
}
