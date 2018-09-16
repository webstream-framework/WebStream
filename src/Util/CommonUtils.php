<?php
namespace WebStream\Util;

use WebStream\Exception\SystemException;

/**
 * CommonUtils
 * 共通のUtility
 * @author Ryuichi Tanaka
 * @since 2015/12/26
 * @version 0.7
 */
trait CommonUtils
{
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
        return lcfirst($this->snake2ucamel($str));
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
}
