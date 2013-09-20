<?php
namespace WebStream\Module;

require_once dirname(__FILE__) . '/Utility.php';

/**
 * クラスローダ
 * @author Ryuichi TANAKA.
 * @since 2013/09/02
 * @version 0.4.2
 */
class ClassLoader
{
    use Utility;

    /** 検索除外ファイルリスト */
    private $ignoreFileList;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->ignoreFileList = ['.', '..', '.git', '.svn', ".DS_Store"];
    }

    /**
     * クラスをロードする
     * @param string|array クラス名
     * @return boolean ロード結果
     */
    public function load($className)
    {
        if (is_array($className)) {
            $includeList = $this->loadClassList($className);
            if ($includeList !== null) {
                foreach ($includeList as $includeFile) {
                    include_once $includeFile;
                }
            } else {
                return false;
            }
        } else {
            $includeFile = $this->loadClass($className);
            if ($includeFile !== null) {
                include_once $includeFile;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * ロード可能なクラスを返却する
     * @param string クラス名
     * @return string ロード可能クラス
     */
    private function loadClass($className)
    {
        $rootDir = $this->getRoot();

        // 名前空間セパレータをパスセパレータに置換
        if (DIRECTORY_SEPARATOR === '/') {
            $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
        }

        // まずcoreディレクトリを検索
        // coreディレクトリは名前空間とディレクトリパスが
        // 紐づいているのでそのまま連結して読ませる
        $includeFile = $rootDir . DIRECTORY_SEPARATOR . "core" . DIRECTORY_SEPARATOR . $className . ".php";
        if (file_exists($includeFile) && is_file($includeFile)) {
            return $includeFile;
        }

        // 名前空間とディレクトリパスが紐づいていない場合、検索する
        $regexp = "/" . preg_replace("/\//", "\\\/", $className . ".php$") . "/";
        $includeFile = $this->searchClass($rootDir . DIRECTORY_SEPARATOR . "core", $regexp);
        if ($includeFile !== null) {
            return $includeFile;
        }

        // それでも見つからなかったらappディレクトリを検索
        // appディレクトリは名前空間とディレクトリパスが
        // 紐づいているとは限らないため検索する
        // 名前空間パスは排除してクラス名で検索する
        $list = preg_split("/\//", $className);
        $className = end($list);

        $includeFile = $this->existModule($rootDir, $className);
        if ($includeFile !== null) {
            return $includeFile;
        }

        return null;
    }

    /**
     * ロード可能なクラスを複数返却する
     * @param array クラス名
     * @return array ロード可能クラスリスト
     */
    private function loadClassList($classList)
    {
        $includeList = [];
        foreach ($classList as $className) {
            $includeFile = $this->loadClass($className);
            if ($includeFile !== null) {
                $includeList[] = $includeFile;
            } else {
                return null;
            }
        }

        return $includeList;
    }

    /**
     * ロード可能なクラスを検索する
     * @param string 検索ディレクトリ
     * @param string 検索正規表現
     * @return string モジュールパス
     */
    private function searchClass($dir, $regexp)
    {
        if (is_dir($dir) && is_readable($dir)) {
            foreach (glob($dir . '*/', GLOB_ONLYDIR) as $c) {
                $ttt = $this->searchClass($c, $regexp);
                if ($ttt !== null) {
                    return $ttt;
                }
            }
            foreach (glob($dir . '*', GLOB_BRACE) as $filepath) {
                if (preg_match($regexp, $filepath)) {
                    return $filepath;
                }
            }
        }
    }

    /**
     * モジュールが存在するかどうかチェックする
     * @param string ディレクトリパス
     * @param string クラス名
     * @return モジュールパス
     */
    private function existModule($currentDir, $className)
    {
        $includeFile  = $currentDir . DIRECTORY_SEPARATOR . $className . ".php";
        if (file_exists($includeFile) && is_file($includeFile) && is_readable($includeFile)) {
            return $includeFile;
        } else {
            // カレントディレクトリを検索
            if (is_dir($currentDir) && is_readable($currentDir) && $dh = opendir($currentDir)) {
                while (false !== ($filename = readdir($dh))) {
                    if (in_array($filename, $this->ignoreFileList)) {
                        continue;
                    }
                    $childDir = $currentDir . DIRECTORY_SEPARATOR . $filename;
                    $modulePath = $this->existModule($childDir, $className);
                    if (is_dir($childDir) && $modulePath !== null) {
                        return $modulePath;
                    }
                }
            }
        }

        return null;
    }
}
