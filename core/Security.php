<?php
/**
 * Securityクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/18
 */
class Security {
    /** 強制置換する文字列と置換文字列を定義 */
    private static $force_replace_str = array(
        '\t' => '&nbsp;&nbsp;&nbsp;&nbsp;',
        '\r\n' => '<br/>',
        '\r' => '<br/>',
        '\n' => '<br/>',
        '\\' => '\\\\',
        '<!--' => '&lt;!--',
        '-->' => '--&gt;',
        '<![CDATA[' => '&lt;![CDATA['
    );
    
    /**
     * CSRFトークンチェック
     */
    public static function isCsrfCheck() {
        $session = Session::start();
        $request = new Request();
        $token = Utility::getCsrfTokenKey();
        $session_token = $session->get($token);
        $request_token = null;
        $isExistParams = false;
            
        // セッションにCSRFトークンがセットされている場合、チェックを実行する
        if (isset($session_token)) {
            // CSRFトークンはワンタイムなので削除する
            $session->delete($token);
            if ($request->isPost()) {
                $request_token = $request->post($token);
                $isExistParams = count($request->getPOST()) >= 2;
            }
            else if ($request->isGet()) {
                $request_token = $request->get($token);
                $isExistParams = count($request->getGET()) >= 2;
            }
            // POSTパラメータが存在し、かつ、CSRFトークンが一致しない場合はCSRFエラーとする
            if ($session_token !== $request_token && $isExistParams) {
                throw new CsrfException("Sent invalid CSRF token");
            }
        }
    }
    
    /**
     * ブラウザから入力されたデータを安全にDBに保存するためのデータに変換する
     * @param String or Array or Hash ブラウザからの入力データ
     * @return String or Array or Hash 安全なデータ
     */
    public static function safetyIn($data) {
        // 渡されたデータが配列の場合、分解して再帰処理
        if (is_array($data)) {
            while (list($key) = each($data)) {
                $data[$key] = self::safetyIn($data[$key]);
            }
            return $data;
        }
        // 制御文字削除
        $data = self::removeInvisibleCharacters($data);
        // URLデコード(スペースを+にしない)
        $data = rawurldecode($data);
        
        return $data;
    }
    
    /**
     * ブラウザに出力するデータを安全なデータに変換する
     * @param String or Array or Hash ブラウザへの出力データ
     * @return String or Array or Hash 安全なデータ
     */
    public static function safetyOut($data) {
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
        foreach (self::$force_replace_str as $key => $val) {
            $data = str_replace($key, $val, $data);
        }
        return $data;
    }
    
    /**
     * 制御文字を削除する
     * @param String 入力文字列
     * @param Boolean URLエンコードされているかどうか
     */
    private static function removeInvisibleCharacters($str, $url_encoded = true) {
        $removes = array();
        if ($url_encoded) {
            $removes[] = '/%0[0-8bcef]/'; // 00-08, 11, 12, 14, 15
            $removes[] = '/%1[0-9a-f]/';  // 16-31
        }
        $removes[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127
        
        // $strが数行にわたっている場合、一度で置換しきれないので繰り返す
        do {
            $str = preg_replace($removes, '', $str, -1, $count);
        }
        while ($count !== 0);
        
        return $str;
    }
}
