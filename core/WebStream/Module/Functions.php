<?php
namespace WebStream\Module;

/**
 * Functions
 * @author Ryuichi TANAKA.
 * @since 2013/09/04
 * @version 0.4
 */

/**
 * ハンドリングできないエラーをハンドリングする
 */
if (!function_exists('shutdownHandler')) {
    function shutdownHandler()
    {
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
 * 出力データに対して安全なデータに変換をする(XML専用)
 */
if (!function_exists('safetyOutXML')) {
    function safetyOutXML($data)
    {
        return Security::safetyOutXML($data);
    }
}
