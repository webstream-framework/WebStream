<?php
namespace WebStream\Module;

use WebStream\Exception\ResourceNotFoundException;

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

        return $isProjectRoot ? $targetPath : null;
    }

    /**
     * ファイル検索する
     * @param string ファイル名
     * @param string 検索起点のディレクトリパス
     * @param integer 深さ(指定しない)
     * @param array ファイルパスリスト(指定しない)
     * @return array 検索結果
     */
    public function fileSearch($className, $dir = null, &$depth = 0, &$filepathList = [])
    {
        if ($dir === null) {
            $dir = $this->getRoot();
        }
        if (is_dir($dir) && is_readable($dir)) {
            foreach (glob($dir . '*/', GLOB_ONLYDIR) as $c) {
                ++$depth;
                $this->fileSearch($className, $c, $depth, $filepathList);
            }
            foreach (glob($dir . '*', GLOB_BRACE) as $filepath) {
                if (strpos($filepath, $className) !== false) {
                    --$depth;
                    $filepathList[] = $filepath;
                }
            }
        }
        if ($depth === 0) {
            return $filepathList;
        }
        --$depth;
    }

    /**
     * ファイル検索する
     * @param string 正規表現
     * @param string 検索起点のディレクトリパス
     * @param integer 深さ(指定しない)
     * @param array ファイルパスリスト(指定しない)
     * @return array 検索結果
     */
    public function fileSearchRegexp($regexp, $dir = null, &$depth = 0, &$filepathList = [])
    {
        if ($dir === null) {
            $dir = $this->getRoot();
        }
        if (is_dir($dir) && is_readable($dir)) {
            foreach (glob($dir . '*/', GLOB_ONLYDIR) as $c) {
                ++$depth;
                $this->fileSearchRegexp($regexp, $c, $depth, $filepathList);
            }
            foreach (glob($dir . '*', GLOB_BRACE) as $filepath) {
                if (preg_match($regexp, $filepath)) {
                    --$depth;
                    $filepathList[] = $filepath;
                }
            }
        }
        if ($depth === 0) {
            return $filepathList;
        }
        --$depth;
    }

    /**
     * 指定したファイルの名前空間を取得する
     * @param string ファイルパス
     * @return string 名前空間
     */
    public function getNamespace($filepath)
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
        if (file_exists($realpath)) {
            return parse_ini_file($realpath);
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
     * キャメルケース文字列をスネークケース文字列に置換する
     * @param string キャメルケース文字列
     * @return string スネークケース文字列
     */
    public function camel2snake($str)
    {
        $str = preg_replace_callback('/([A-Z])/', function($matches) {
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
        $str = ucfirst(preg_replace_callback('/_([a-zA-Z])/', function($matches) {
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
}
