<?php
namespace WebStream;
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
