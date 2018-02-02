<?php
namespace WebStream\Util;

/**
 * Securityクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/18
 * @version 0.7
 */
class Security
{
    /**
     * ブラウザから入力されたデータを安全にDBに保存するためのデータに変換する
     * @param String or Array or Hash ブラウザからの入力データ
     * @return String or Array or Hash 安全なデータ
     */
    public static function safetyIn($data)
    {
        // 文字列、配列以外のデータは置換しない
        if (!is_string($data) && !is_array($data)) {
            return $data;
        }
        // 渡されたデータが配列の場合、分解して再帰処理
        if (is_array($data)) {
            $map = [];
            foreach ($data as $key => $value) {
                $map[$key] = $value;
            }

            return $map;
        }
        // 制御文字削除
        $removes = [];
        $removes[] = '/%0[0-8bcef]/'; // 00-08, 11, 12, 14, 15
        $removes[] = '/%1[0-9a-f]/';  // 16-31
        $removes[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127

        // $dataが数行にわたっている場合、一度で置換しきれないので繰り返す
        do {
            $data = preg_replace($removes, '', $data, -1, $count);
        } while ($count !== 0);

        // URLデコード(スペースを+にしない)
        $data = rawurldecode($data);

        return $data;
    }

    /**
     * ブラウザに出力するデータを安全なデータに変換する
     * @param mixed ブラウザへの出力データ
     * @return mixed 安全なデータ
     */
    public static function safetyOut($data)
    {
        // 文字列、配列以外のデータは置換しない
        if (!is_string($data) && !is_array($data)) {
            return $data;
        }
        // 渡されたデータが配列の場合、分解して再帰処理
        if (is_array($data)) {
            while (list($key) = each($data)) {
                $data[$key] = self::safetyOut($data[$key]);
            }

            return $data;
        }
        // <, >, &, ", ' をエスケープ(実体参照置換)する
        $data = htmlspecialchars($data, ENT_QUOTES, "UTF-8");

        // 強制置換対象の文字を変換する
        $forceReplaceHtmlMap = [
            "\t"        => '&nbsp;&nbsp;&nbsp;&nbsp;',
            "\r\n"      => '<br/>',
            "\r"        => '<br/>',
            "\n"        => '<br/>',
            "\\"        => '\\\\',
            '<!--'      => '&lt;!--',
            '-->'       => '--&gt;',
            '<![CDATA[' => '&lt;![CDATA['
        ];
        foreach ($forceReplaceHtmlMap as $key => $val) {
            $data = str_replace($key, $val, $data);
        }

        return $data;
    }

    /**
     * ブラウザに出力するJavaScriptコードを安全なコードに変換する
     * @param mixed ブラウザへの出力データ
     * @return mixed 安全なデータ
     */
    public static function safetyOutJavaScript($data)
    {
        // 文字列、配列以外のデータは置換しない
        if (!is_string($data) && !is_array($data)) {
            return $data;
        }
        // 渡されたデータが配列の場合、分解して再帰処理
        if (is_array($data)) {
            while (list($key) = each($data)) {
                $data[$key] = self::safetyOutJavaScript($data[$key]);
            }

            return $data;
        }

        // 強制置換対象の文字
        $forceReplaceCharacterMap = [
            "\u2028" => "\n",
            "\u2029" => "\n",
            "\u0085" => "\n",
            '\\'     => "\\\\",
            "'"      => '\u0022',
            "\""     => '\u0027',
            "/"      => "\/",
            "<"      => '\x3c',
            ">"      => '\x3e',
            "\r\n"   => "\n",
            "\r"     => "\n",
            "\v"     => '\u000B',
            "\f"     => '\u000C',
            "\n"     => '\u000D\u000A'
        ];

        foreach ($forceReplaceCharacterMap as $key => $val) {
            $data = str_replace($key, $val, $data);
        }

        return $data;
    }

    /**
     * ブラウザに出力するXMLデータを安全なデータに変換する
     * @param string ブラウザへの出力XML
     * @return string 安全なXML
     */
    public static function safetyOutXML($data)
    {
        return htmlspecialchars($data, ENT_XML1, "UTF-8");
    }
}
