<?php
/**
 * ファイルのインポートをする
 * @param filepath インポートするファイルパス
 * @return boolean インポート結果
 */
if (!function_exists('import')) {
    function import($filepath) {
        return AutoImport::import($filepath);
    }
}

/**
 * フォルダ内のすべてのファイルをインポートする
 * @param dirpath インポート対象のフォルダ
 * @return インクルードしたファイルの絶対パス
 */
if (!function_exists('importAll')) {
    function importAll($dirpath) {
        return AutoImport::importAll($dirpath);
    }
}

/**
 * 自動インポートクラス
 * @author Ryuichi TANAKA.
 * @since 2011/08/19
 */
class AutoImport {
    private function __construct() {}
    
    /**
     * ライブラリディレクトリからインポートする
     * @param String ファイル名
     * @return boolean インポート結果
     */
    public static function import($filepath) {
        // プロジェクトルートパス
        $path = self::getRoot();

        // 正規化した絶対パス
        $realpath = $path . DIRECTORY_SEPARATOR . $filepath . ".php";
        if (realpath($realpath)) {
            include_once($realpath);
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * 指定したディレクトリ配下にあるファイルをすべてインクルードする
     * @param ルートディレクトリからの相対パス
     * @return インクルードしたファイルの絶対パス
     */
    public static function importAll($dirpath) {
        // プロジェクトルートパス
        $path = self::getRoot();
    
        // 正規化した絶対パス
        $realpath = $path . DIRECTORY_SEPARATOR . $dirpath;
    
        // 絶対パスが存在してディレクトリかどうか
        $includes = array();
        if (file_exists($realpath) && is_dir($realpath) && $dh = opendir($realpath)) {
            while (false !== ($filename = readdir($dh))) {
                $php_filepath = $realpath . DIRECTORY_SEPARATOR . $filename;

                // PHPファイルならインクルードする
                if (is_file($php_filepath) &&
                    pathinfo($filename, PATHINFO_EXTENSION) == "php") {
                    // AutoImport::importで読み込む形式に変換
                    $import_path = $dirpath . "/" . pathinfo($filename, PATHINFO_FILENAME);
                    if (self::import($import_path)) {
                        $includes[] = $filename;
                    }
                }
            }
        }
        return $includes;
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
}