<?php
namespace WebStream;

/**
 * ハンドリングできないエラーをハンドリングする
 */
if (!function_exists('shutdownHandler')) {
    function shutdownHandler() {
        $isError = false;
        if ($error = error_get_last()){
            switch($error['type']){
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_CORE_WARNING:
            case E_COMPILE_ERROR:
            case E_COMPILE_WARNING:
                $isError = true;
                break;
            }
        }
        if ($isError){
            $errorMsg = $error['message'] . " " . $error['file'] . "(" . $error['line'] . ")";
            Logger::fatal($errorMsg);
        }
    }
}

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
 * 入力データに対して安全なデータに変換をする
 * @param String or Array or Hash 入力データ
 * @return String or Array or Hash 安全なデータ
 */
if (!function_exists('safetyIn')) {
    function safetyIn($data) {
        return Security::safetyIn($data);
    }
}

/**
 * フォルダ内のすべてのファイルをインポートする
 * @param String or Array or Hash 出力データ
 * @return String or Array or Hash 安全なデータ
 */
if (!function_exists('safetyOut')) {
    function safetyOut($data) {
        return Security::safetyOut($data);
    }
}
