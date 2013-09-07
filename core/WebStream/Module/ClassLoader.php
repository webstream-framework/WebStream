<?php
namespace WebStream\Module;

require_once dirname(__FILE__) . '/Utility.php';

/**
 * クラスローダ
 * @author Ryuichi TANAKA.
 * @since 2013/09/02
 * @version 0.4
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
        $this->ignoreFileList = ['.', '..', '.git', '.svn'];
    }

    /**
     * クラスをロードする
     * @param string クラスパス
     */
    public function load($className)
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
            include_once($includeFile);
            return;
        }

        // それでも見つからなかったらappディレクトリを検索
        // appディレクトリは名前空間とディレクトリパスが
        // 紐づいているとは限らないため検索する
        // 名前空間パスは排除してクラス名で検索する
        $list = preg_split("/\//", $className);
        $className = end($list);

        $includeFile = $this->existModule($rootDir, $className);
        if ($includeFile !== null) {
            include_once($includeFile);
            return;
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
        if (file_exists($includeFile) && is_file($includeFile)) {
            return $includeFile;
        } else {
            // カレントディレクトリを検索
            if (is_dir($currentDir) && $dh = opendir($currentDir)) {
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
