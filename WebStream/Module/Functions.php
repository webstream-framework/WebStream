<?php
/**
 * Functions
 * @author Ryuichi TANAKA.
 * @since 2013/09/04
 * @version 0.7
 */

use WebStream\Module\Security;
use WebStream\Module\ServiceLocator;

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
