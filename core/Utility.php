<?php
namespace WebStream;
/**
 * Utilityクラス
 * @author Ryuichi Tanaka
 * @since 2011/09/10
 */
class Utility {
    /** インスタンス化禁止 */
    private function __construct() {}
    
    /**
     * CSRFトークンを保存するキー
     * @return String キー
     */
    public static function getCsrfTokenKey() {
        return "__CSRF_TOKEN__";
    }
    
    /**
     * プロジェクトディレクトリ名を返却する
     * @return String プロジェクトディレクトリ名
     */
    public static function getProjectName() {
        $pjname = null;
        if (preg_match('/.*\/(.*)/', self::getRoot(), $matches)) {
            $pjname = $matches[1];
        }
        return $pjname;
    }
    
    /**
     * プロジェクトディレクトリの絶対パスを返す
     * @return String プロジェクトディレクトリの絶対パス
     */
    public static function getRoot() {
        // 現在のディレクトリパス
        $current = dirname(__FILE__);
        
        // プロジェクトルートまでの絶対パスを取得
        $path_hierarchy_list = explode(DIRECTORY_SEPARATOR, $current);
        
        // 現在のディレクトリパスの1階層上がプロジェクトルートである
        // と定義するので、単純に最後のディレクトリをカットする
        array_pop($path_hierarchy_list);
        $project_root = implode("/", $path_hierarchy_list);
        
        return is_dir($project_root) ? $project_root : null;
    }
    
    /**
     * 設定ファイルをパースする
     * @param String プロジェクトルートからの相対パス
     * @return Hash 設定情報
     */
    public static function parseConfig($filepath) {
        // プロジェクトルートパス
        $root_path = self::getRoot();

        // 正規化した絶対パス
        $realpath = $root_path . DIRECTORY_SEPARATOR . $filepath;
        if (realpath($realpath)) {
            return parse_ini_file($realpath);
        }
    }

    /**
     * ランダムな文字列を生成して返却する
     * @param int 生成する文字数(省略時は10文字)
     * @return String ランダム文字列
     */
    public static function getRandomString($length = 10) {
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
     * @param String キャメルケース文字列
     * @return String スネークケース文字列
     */
    public static function camel2snake($str) {
        $str = preg_replace_callback('/([A-Z])/', function($matches) {
            return '_' . lcfirst($matches[1]);
        }, $str);
        return preg_replace('/^_/', '', $str);
    }
    
    /**
     * スネークケース文字列をアッパーキャメルケースに置換する
     * @param String スネークケース文字列
     * @return String アッパーキャメルケース文字列
     */
    public static function snake2ucamel($str) {
        $str = ucfirst(preg_replace_callback('/_([a-zA-Z])/', function($matches) {
            return ucfirst($matches[1]);
        }, $str));
        return $str;
    }
    
    /**
     * スネークケース文字列をローワーキャメルケースに置換する
     * @param String スネークケース文字列
     * @return String ローワーキャメルケース文字列
     */
    public static function snake2lcamel($str) {
        return lcfirst(self::snake2ucamel($str));
    }
    
    /**
     * XMLオブジェクトを配列に変換する
     * @param Object XMLオブジェクト
     * @return Hash 配列/ハッシュデータ
     */
    public static function xml2array($xml) {
        $result = array();
        if (is_object($xml)) {
            $list = get_object_vars($xml);
            while (list($k, $v) = each($list)) {
                $result[$k] = Utility::xml2array($v);
            }
        }
        else if (is_array($xml)) {
            while (list($k, $v) = each($xml)) {
                $result[$k] = Utility::xml2array($v);
            }
        }
        else {
            $result = $xml;
        }
        return $result;
    }
    
   /**
     * ファイルからmimeタイプを返却する
     * @param String ファイルタイプ
     * @return String mimeタイプ
     */
    public static function getMimeType($type) {
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
     * @param String 文字列
     * @return String バイト長
     */
    public static function bytelen($data) {
        return strlen(bin2hex($data)) / 2;
    }

    /**
     * データをシリアライズ化してテキストデータにエンコードする
     * @param Object 対象データ
     * @return String エンコードしたデータ
     */
    public static function encode($data) {
        return base64_encode(serialize($data));
    }

    /**
     * データをデシリアライズ化して元のデータをデコードする
     * @param String エンコード済みデータ
     * @return Object デコードしたデータ
     */
    public static function decode($data) {
        return unserialize(base64_decode($data));
    }
}
