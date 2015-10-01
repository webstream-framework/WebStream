<?php
/**
 * Functions
 * @author Ryuichi TANAKA.
 * @since 2013/09/04
 * @version 0.4
 */

use WebStream\Module\Logger;
use WebStream\Module\Security;
use WebStream\DI\ServiceLocator;

/**
 * ハンドリングできないエラーをハンドリングする
 */
if (!function_exists('shutdownHandler')) {
    function shutdownHandler()
    {
        // サービスロケータをクリア
        ServiceLocator::removeContainer();

        // ログ処理
        if ($error = error_get_last()) {
            $errorMsg = $error['message'] . " " . $error['file'] . "(" . $error['line'] . ")";
            switch ($error['type']) {
                case E_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                case E_RECOVERABLE_ERROR:
                    Logger::fatal($errorMsg);
                    break;
                case E_PARSE:
                    Logger::error($errorMsg);
                    break;
                case E_WARNING:
                case E_CORE_WARNING:
                case E_COMPILE_WARNING:
                case E_USER_WARNING:
                case E_STRICT:
                case E_NOTICE:
                case E_USER_NOTICE:
                case E_DEPRECATED:
                case E_USER_DEPRECATED:
                    Logger::warn($errorMsg);
                    break;
            }
        }

        // オブジェクトを解放
        Logger::finalize();
    }
}

/**
 * 入力データに対して安全なデータに変換をする
 */
if (!function_exists('safetyIn')) {
    function safetyIn($data)
    {
        return Security::safetyIn($data);
    }
}

/**
 * 出力データに対して安全なデータに変換をする
 */
if (!function_exists('safetyOut')) {
    function safetyOut($data)
    {
        return Security::safetyOut($data);
    }
}

/**
 * 出力データに対して安全なデータに変換をする(JavaScript専用)
 */
if (!function_exists('safetyOutJavaScript')) {
    function safetyOutJavaScript($data)
    {
        return Security::safetyOutJavaScript($data);
    }
}

/**
 * 出力データに対して安全なデータに変換をする(XML専用)
 */
if (!function_exists('safetyOutXML')) {
    function safetyOutXML($data)
    {
        return Security::safetyOutXML($data);
    }
}

/**
 * 出力データに対して安全なデータに変換をする(JSON専用)
 */
if (!function_exists('safetyOutJSON')) {
    function safetyOutJSON($data)
    {
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
}

/**
 * 出力データに対して安全なデータに変換をする(JSON専用)
 */
if (!function_exists('safetyOutJSONP')) {
    function safetyOutJSONP($data, $callback)
    {
        return safetyOutJavaScript($callback) . "(" . safetyOutJSON($data) . ");";
    }
}
