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

    /** アプリケーションルートパス */
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
        $includeFile = $this->getRoot() . "/" . $filepath;
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
        $includeDir = realpath($this->getRoot() . "/" . $dirPath);
        if (is_dir($includeDir)) {
            $iterator = $this->getFileSearchIterator($includeDir);
            $isSuccess = true;
            foreach ($iterator as $filepath => $fileObject) {
                if ($filepath === $includeDir . "/." || $filepath === $includeDir . "/..") {
                    continue;
                }
                if (is_file($filepath)) {
                    include_once $filepath;
                    Logger::debug($filepath . " import success.");
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
        if (is_file($includeFile)) {
            include_once $includeFile;

            return;
        }

        // さらに見つからなかったらappディレクトリを名前空間付きで全検索
        $iterator = $this->getFileSearchIterator($this->applicationRoot . "/app");
        foreach ($iterator as $filepath => $fileObject) {
            if (strpos($filepath, $className . ".php") !== false) {
                include_once $filepath;

                return;
            }
        }

        // 名前空間とディレクトリ構成が一致していない場合、クラス名を抜き出して、マッチするもの全てをincludeする
        if (preg_match("/(?:.*\/){0,}(.+)/", $className, $matches)) {
            $classNameWithoutNamespace = $matches[1];
            // この処理が走るケースはapp配下のクラスがディレクトリ構成と名前空間が一致していない
            // 場合以外ない(テスト用クラス除く)ので、app配下の検索を優先する
            $isInclude = false;
            $iterator = $this->getFileSearchIterator($this->applicationRoot . "/app");
            foreach ($iterator as $filepath => $fileObject) {
                if (strpos($filepath, $classNameWithoutNamespace . ".php") !== false) {
                    include_once $filepath;
                    $isInclude = true;
                }
            }
            if ($isInclude) {
                return;
            }

            // ここに到達するのはテスト用クラスのみ
            $iterator = $this->getFileSearchIterator($rootDir . "/core");
            foreach ($iterator as $filepath => $fileObject) {
                if (strpos($filepath, $classNameWithoutNamespace . ".php") !== false) {
                    include_once $filepath;
                    $isInclude = true;
                }
            }
            if ($isInclude) {
                return;
            }
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
}
