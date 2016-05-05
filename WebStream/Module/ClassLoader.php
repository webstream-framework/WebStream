<?php
namespace WebStream\Module;

require_once dirname(__FILE__) . '/Utility/FileUtils.php';
require_once dirname(__FILE__) . '/../DI/Injector.php';

use WebStream\Module\Utility\ApplicationUtils;
use WebStream\Module\Utility\FileUtils;
use WebStream\DI\Injector;

/**
 * クラスローダ
 * @author Ryuichi TANAKA.
 * @since 2013/09/02
 * @version 0.7
 */
class ClassLoader
{
    use Injector, FileUtils;

    /**
     * @var string アプリケーションルートパス
     */
    private $applicationRoot;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->applicationRoot = $this->getApplicationRoot();
    }

    /**
     * クラスをロードする
     * @param string|array クラス名
     * @return array<string> ロード済みクラスリスト
     */
    public function load($className)
    {
        return is_array($className) ? $this->loadClassList($className) : $this->loadClass($className);
    }

    /**
     * ファイルをインポートする
     * @param string ファイルパス
     * @param callable フィルタリング無名関数 trueを返すとインポート
     * @return boolean インポート結果
     */
    public function import($filepath, callable $filter = null)
    {
        $includeFile = $this->applicationRoot . "/" . $filepath;
        if (is_file($includeFile)) {
            $ext = pathinfo($includeFile, PATHINFO_EXTENSION);
            if ($ext === 'php') {
                if ($filter === null || (is_callable($filter) && $filter($includeFile) === true)) {
                    include_once $includeFile;
                    $this->logger->debug($includeFile . " import success.");
                }
            }

            return true;
        }

        return false;
    }

    /**
     * 指定ディレクトリのファイルをインポートする
     * @param string ディレクトリパス
     * @param callable フィルタリング無名関数 trueを返すとインポート
     * @return boolean インポート結果
     */
    public function importAll($dirPath, callable $filter = null)
    {
        $includeDir = realpath($this->applicationRoot . "/" . $dirPath);
        if (is_dir($includeDir)) {
            $iterator = $this->getFileSearchIterator($includeDir);
            $isSuccess = true;
            foreach ($iterator as $filepath => $fileObject) {
                if (preg_match("/(?:\/\.|\/\.\.|\.DS_Store)$/", $filepath)) {
                    continue;
                }
                if (is_file($filepath)) {
                    $ext = pathinfo($filepath, PATHINFO_EXTENSION);
                    if ($ext === 'php') {
                        if ($filter === null || (is_callable($filter) && $filter($filepath) === true)) {
                            include_once $filepath;
                            $this->logger->debug($filepath . " import success.");
                        }
                    }
                } else {
                    $this->logger->warn($filepath . " import failure.");
                    $isSuccess = false;
                }
            }
        }

        return $isSuccess;
    }

    /**
     * ロード可能なクラスを返却する
     * @param string クラス名(フルパス指定の場合はクラスパス)
     * @return array<string> ロード可能クラス
     */
    private function loadClass($className)
    {
        $rootDir = $this->applicationRoot;

        // 名前空間セパレータをパスセパレータに置換
        if (DIRECTORY_SEPARATOR === '/') {
            $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
        }

        // appディレクトリを名前空間付きで全検索し、マッチするもの全てをincludeする
        $iterator = $this->getFileSearchIterator($rootDir . "/app");
        $includeList = [];
        foreach ($iterator as $filepath => $fileObject) {
            if (strpos($filepath, $className . ".php") !== false) {
                include_once $filepath;
                $includeList[] = $filepath;
                $this->logger->debug($filepath . " load success. (search from " . $rootDir . "/app/)");
            }
        }
        if (!empty($includeList)) {
            return $includeList;
        }

        // 名前空間とディレクトリ構成が一致していない場合、クラス名を抜き出して、マッチするもの全てをincludeする
        $includeList = [];
        if (preg_match("/(?:.*\/){0,}(.+)/", $className, $matches)) {
            $classNameWithoutNamespace = $matches[1];
            // この処理が走るケースはapp配下のクラスがディレクトリ構成と名前空間が一致していない
            // 場合以外ない(テスト用クラス除く)ので、app配下の検索を優先する
            $iterator = $this->getFileSearchIterator($rootDir . "/app");
            foreach ($iterator as $filepath => $fileObject) {
                if (strpos($filepath, $classNameWithoutNamespace . ".php") !== false) {
                    include_once $filepath;
                    $includeList[] = $filepath;
                    $this->logger->debug($filepath . " load success. (full search)");
                }
            }
            if (!empty($includeList)) {
                return $includeList;
            }

            // プロジェクトルート配下すべてのディレクトリを検索する
            $iterator = $this->getFileSearchIterator($rootDir . "/core");
            foreach ($iterator as $filepath => $fileObject) {
                if (strpos($filepath, $classNameWithoutNamespace . ".php") !== false) {
                    include_once $filepath;
                    $includeList[] = $filepath;
                    $this->logger->debug($filepath . " load success. (full search, use in test)");
                }
            }
            if (!empty($includeList)) {
                return $includeList;
            }
        }

        return $includeList;
    }

    /**
     * ロード可能なクラスを複数返却する
     * @param array クラス名
     * @return array<string> ロード済みクラスリスト
     */
    private function loadClassList($classList)
    {
        $includedlist = [];
        foreach ($classList as $className) {
            $result = $this->loadClass($className);
            if (is_array($result)) {
                $includedlist = array_merge($includedlist, $result);
            }
        }

        return $includedlist;
    }
}
