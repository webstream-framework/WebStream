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
                if (is_array($includeFile)) {
                    foreach ($includeFile as $filepath) {
                        include_once $filepath;
                    }
                } else {
                    include_once $includeFile;
                }
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * ファイルをインポートする
     * @param string ファイルパス
     * @return boolean インポート結果
     */
    public function import($filePath)
    {
        $includeFile = $this->getRoot() . DIRECTORY_SEPARATOR . $filePath;
        if (file_exists($includeFile)) {
            include_once $includeFile;

            return true;
        }

        return false;
    }

    /**
     * 指定ディレクトリのファイルをインポートする
     * @param string ディレクトリパス
     * @return boolean インポート結果
     */
    public function importAll($dirPath)
    {
        $includeDir = realpath($this->getRoot() . DIRECTORY_SEPARATOR . $dirPath);
        if (is_dir($includeDir)) {
            $classList = $this->fileSearchRegexp("/.+\.php$/", $includeDir . "/");
            foreach ($classList as $includeFile) {
                if (file_exists($includeFile)) {
                    include_once $includeFile;
                } else {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * ロード可能なクラスを返却する
     * @param string クラス名
     * @return string|array ロード可能クラス
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

        // 次にcoreディレクトリを名前空間付きで全検索する
        $includeFile = $this->existModule($rootDir . "/core", $className);
        if ($includeFile !== null) {
            return $includeFile;
        }

        // それでも見つからなかったらappディレクトリを名前空間付きで全検索
        $includeFile = $this->existModule($rootDir . "/app", $className);
        if ($includeFile !== null) {
            return $includeFile;
        }

        // 名前空間を除去し、クラス名を抽出する
        $list = preg_split("/\//", $className);
        $className = end($list);

        // さらに見つからない場合は、coreディレクトリをファイル名で全検索する
        $regexp = "/" . preg_replace("/\//", "\\\/", $className) . "/";
        $includeList = $this->fileSearchRegexp($regexp, $rootDir . DIRECTORY_SEPARATOR . "core/WebStream");
        if (count($includeList) > 0) {
            return $includeList;
        }

        // なおも見つからない場合は、appディレクトリをファイル名で全検索する
        $includeList = $this->fileSearchRegexp($regexp, $rootDir . DIRECTORY_SEPARATOR . "app");
        if (count($includeList) > 0) {
            return $includeList;
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
