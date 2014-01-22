<?php
namespace WebStream\Module;

use WebStream\Module\FileSearchIterator;
use WebStream\Module\Logger;

require_once dirname(__FILE__) . '/Utility.php';
require_once dirname(__FILE__) . '/Logger.php';
require_once dirname(__FILE__) . '/FileSearchIterator.php';

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
     */
    public function load($className)
    {
        if (is_array($className)) {
            $this->loadClassList($className);
        } else {
            $this->loadClass($className);
        }
    }

    /**
     * ファイルをインポートする
     * @param string ファイルパス
     * @return boolean インポート結果
     */
    public function import($filepath)
    {
        $includeFile = $this->getRoot() . DIRECTORY_SEPARATOR . $filepath;
        if (file_exists($includeFile)) {
            include_once $includeFile;
            Logger::debug($includeFile . " import success.");
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
            $baseDir = $this->getRoot() . "/" . $dirPath;
            $iterator = new FileSearchIterator($baseDir, "/.+\.php$/");
            $isSuccess = true;
            foreach ($iterator as $filepath => $object) {
                if (file_exists($filepath)) {
                    include_once $filepath;
                    Logger::debug($filepath . " import success.");
                } else {
                    Logger::error($filepath . " import failure.");
                    $isSuccess = false;
                }
            }
        }

        return $isSuccess;
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
        $includeFile = $rootDir . "/core/" . $className . ".php";
        if (file_exists($includeFile) && is_file($includeFile)) {
            include_once $includeFile;

            return;
        }

        // 次にcoreディレクトリを名前空間付きで全検索する
        $includeFile = $this->existModule($rootDir . "/core", $className);
        if ($includeFile !== null) {
            include_once $includeFile;

            return;
        }

        // それでも見つからなかったらappディレクトリを名前空間付きで全検索
        // TODO /app固定にするとテスト用appが検索されない。ApplicationのようにまたはDIで環境依存変数を注入するようにしたい。
        $includeFile = $this->existModule($rootDir . "/app", $className);
        if ($includeFile !== null) {
            include_once $includeFile;

            return;
        }

        // 名前空間を除去し、クラス名を抽出する
        $list = preg_split("/\//", $className);
        $className = end($list);

        // さらに見つからない場合は、coreディレクトリをファイル名で全検索する
        $regexp = "/" . preg_replace("/\//", "\\\/", $className) . "\.php$/";
        $iterator = new FileSearchIterator($rootDir . "/core/WebStream", $regexp);
        $isInclude = false;
        foreach ($iterator as $filepath => $object) {
            include_once $filepath;
            $isInclude = true;
        }

        if ($isInclude) {
            return;
        }

        // なおも見つからない場合は、appディレクトリをファイル名で全検索する
        // TODO ここも。
        $regexp = "/" . preg_replace("/\//", "\\\/", $className) . "\.php$/";
        $iterator = new FileSearchIterator($rootDir . "/app", $regexp);
        $isInclude = false;
        foreach ($iterator as $filepath => $object) {
            include_once $filepath;
            $isInclude = true;
        }

        if ($isInclude) {
            return;
        }
    }

    /**
     * ロード可能なクラスを複数返却する
     * @param array クラス名
     */
    private function loadClassList($classList)
    {
        foreach ($classList as $className) {
            $this->loadClass($className);
        }
    }

    /**
     * モジュールが存在するかどうかチェックする
     * FileSearchIteratorと異なり、指定したクラスパスを起点とした再帰検索なのでオーダはそれほど多くない
     * @param string ディレクトリパス
     * @param string クラスパス
     * @return モジュールパス
     */
    private function existModule($currentDir, $classpath)
    {
        $includeFile  = $currentDir . DIRECTORY_SEPARATOR . $classpath . ".php";
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
                    $modulePath = $this->existModule($childDir, $classpath);
                    if (is_dir($childDir) && $modulePath !== null) {
                        return $modulePath;
                    }
                }
            }
        }

        return null;
    }
}
