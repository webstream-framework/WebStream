<?php
namespace WebStream\Module;

require_once dirname(__FILE__) . '/Utility.php';
require_once dirname(__FILE__) . '/Logger.php';

/**
 * クラスローダ
 * @author Ryuichi TANAKA.
 * @since 2013/09/02
 * @version 0.4.2
 */
class ClassLoader
{
    use Utility;

    /**
     * @var string アプリケーションルートパス
     */
    private $applicationRoot;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->applicationRoot = $this->getRoot();
    }

    /**
     * テスト環境を設定する
     */
    public function test()
    {
        $this->applicationRoot = $this->getTestApplicationRoot();
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
        $includeFile = $this->getRoot() . "/" . $filepath;
        if (is_file($includeFile)) {
            $ext = pathinfo($includeFile, PATHINFO_EXTENSION);
            if ($ext === 'php') {
                if ($filter === null || (is_callable($filter) && $filter($includeFile) === true)) {
                    include_once $includeFile;
                    Logger::debug($includeFile . " import success.");
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
        $includeDir = realpath($this->getRoot() . "/" . $dirPath);
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
                            Logger::debug($filepath . " import success.");
                        }
                    }
                } else {
                    Logger::warn($filepath . " import failure.");
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
        $includeList = [];
        $rootDir = $this->getRoot();

        // 名前空間セパレータをパスセパレータに置換
        if (DIRECTORY_SEPARATOR === '/') {
            $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
        }

        // まずcoreディレクトリを検索
        // coreディレクトリは名前空間とディレクトリパスが
        // 紐づいているのでそのまま連結して読ませる
        $includeFile = $rootDir . "/core/" . $className . ".php";
        if (is_file($includeFile)) {
            include_once $includeFile;
            Logger::debug($includeFile . " load success. (search from " . $rootDir . "/core/)");

            return [$includeFile];
        }

        // さらに見つからなかったらappディレクトリを名前空間付きで全検索
        $iterator = $this->getFileSearchIterator($this->applicationRoot . "/app");
        foreach ($iterator as $filepath => $fileObject) {
            if (strpos($filepath, $className . ".php") !== false) {
                include_once $filepath;
                Logger::debug($filepath . " load success. (search from " . $this->applicationRoot . "/app/)");

                return [$filepath];
            }
        }

        // 名前空間とディレクトリ構成が一致していない場合、クラス名を抜き出して、マッチするもの全てをincludeする
        if (preg_match("/(?:.*\/){0,}(.+)/", $className, $matches)) {
            $classNameWithoutNamespace = $matches[1];
            // この処理が走るケースはapp配下のクラスがディレクトリ構成と名前空間が一致していない
            // 場合以外ない(テスト用クラス除く)ので、app配下の検索を優先する
            $iterator = $this->getFileSearchIterator($this->applicationRoot . "/app");
            foreach ($iterator as $filepath => $fileObject) {
                if (strpos($filepath, $classNameWithoutNamespace . ".php") !== false) {
                    include_once $filepath;
                    $includeList[] = $filepath;
                    Logger::debug($filepath . " load success. (full search)");
                }
            }
            if (!empty($includeList)) {
                return $includeList;
            }

            // ここに到達するのはテスト用クラスのみ
            $iterator = $this->getFileSearchIterator($rootDir . "/core");
            foreach ($iterator as $filepath => $fileObject) {
                if (strpos($filepath, $classNameWithoutNamespace . ".php") !== false) {
                    include_once $filepath;
                    $includeList[] = $filepath;
                    Logger::debug($filepath . " load success. (full search, use in test)");
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
