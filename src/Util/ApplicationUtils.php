<?php
namespace WebStream\Util;

use WebStream\Exception\SystemException;

/**
 * ApplicationUtils
 * アプリケーション依存のUtility
 * @author Ryuichi Tanaka
 * @since 2015/12/26
 * @version 0.7
 */
trait ApplicationUtils
{
    /**
     * プロジェクトディレクトリの絶対パスを返す
     * @return string プロジェクトディレクトリの絶対パス
     */
    public function getApplicationRoot()
    {
        // 上位階層を辿り、.projectrootファイルを見つける
        $targetPath = realpath(dirname(__FILE__));
        $isProjectRoot = false;

        while (!$isProjectRoot) {
            if (file_exists($targetPath . DIRECTORY_SEPARATOR . $this->getProjectFileName())) {
                $isProjectRoot = true;
            } else {
                if (preg_match("/(.*)\//", $targetPath, $matches)) {
                    $targetPath = $matches[1];
                    if (!is_dir($targetPath)) {
                        break;
                    }
                }
            }
        }

        if (!$isProjectRoot) {
            throw new SystemException("'.projectroot' file must be put in directly under the project directory.");
        }

        return $targetPath;
    }

    /**
     * 指定したファイルの名前空間を取得する
     * @param string ファイルパス
     * @param string 起点ディレクトリパス
     * @return string 名前空間
     */
    public function getNamespace($filepath, $baseDir = null)
    {
        if (file_exists($filepath)) {
            $resource = fopen($filepath, "r");
            while (false !== ($line = fgets($resource))) {
                if (preg_match("/^namespace\s(.*);$/", $line, $matches)) {
                    $namespace = $matches[1];
                    if (substr($namespace, 0) !== '\\') {
                        $namespace = '\\' . $namespace;
                    }

                    return $namespace;
                }
            }
            fclose($resource);
        }

        return null;
    }

    /**
     * Viewで有効なModel変数名を返却する
     * @return string Model変数名
     */
    public function getModelVariableName()
    {
        return "model";
    }

    /**
     * Viewで有効なHelper変数名を返却する
     * @return string Helper変数名
     */
    public function getHelperVariableName()
    {
        return "helper";
    }

    /**
     * プロジェクトルートファイル名を返却する
     * @return string プロジェクトルートファイル名
     */
    private function getProjectFileName()
    {
        return ".projectroot";
    }

    /**
     * CoreHelper#asyncで使用するIDを返却する
     * @return string DOMID
     */
    public function getAsyncDomId()
    {
        return $this->getRandomstring(32);
    }

    /**
     * CoreHelper#asyncで使用するコードを返却する
     * @param string URL
     * @param string CSSクラス名
     * @return string コード
     */
    public function asyncHelperCode($url, $id)
    {
        return <<< JSCODE
(function (c,b) {var a;a=window.XMLHttpRequest?new XMLHttpRequest:new ActiveXObject("Microsoft.XMLHTTP");a.onreadystatechange=function () {4==a.readyState&&200==a.status&&(document.getElementById(b).outerHTML=a.responseText)};a.open("GET",c,!0);a.send()})("$url","$id");
JSCODE;
    }
}
