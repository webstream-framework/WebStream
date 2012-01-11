<?php
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
}
