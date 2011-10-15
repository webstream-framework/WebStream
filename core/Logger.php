<?php
/**
 * Loggerクラス
 * @author Ryuichi Tanaka
 * @since 2011/09/04
 */
class Logger {
    const DEBUG = 1;
    const INFO  = 2;
    const WARN  = 3;
    const ERROR = 4;
    const FATAL = 5;
    
    /** ログファイルが格納されるディレクトリ */
    private static $log_dir = "log";
    
    /** ログファイル名 */
    private static $log_filename = "stream.log";
    
    /** デフォルトのログレベル */
    public static $level = self::INFO;
    
    private function __construct() {}
    
    /**
     * ログレベル「DEBUG」のログ出力
     * @param String ログメッセージ
     */
    public static function debug($msg, $stacktrace = null) {
        $msg = self::message($msg, $stacktrace);
        self::write($msg, self::DEBUG, "DEBUG");
    }
    
    /**
     * ログレベル「」のログ出力
     * @param String ログメッセージ
     */
    public static function info($msg, $stacktrace = null) {
        $msg = self::message($msg, $stacktrace);
        self::write($msg, self::INFO, "INFO");
    }
    
    /**
     * ログレベル「WARN」のログ出力
     * @param String ログメッセージ
     */
    public static function warn($msg, $stacktrace = null) {
        $msg = self::message($msg, $stacktrace);
        self::write($msg, self::WARN, "WARN");
    }
    
    /**
     * ログレベル「ERROR」のログ出力
     * @param String ログメッセージ
     */
    public static function error($msg, $stacktrace = null) {
        $msg = self::message($msg, $stacktrace);
        self::write($msg, self::ERROR, "ERROR");
    }
    
    /**
     * ログレベル「FATAL」のログ出力
     * @param String ログメッセージ
     */
    public static function fatal($msg, $stacktrace = null) {
        $msg = self::message($msg, $stacktrace);
        self::write($msg, self::FATAL, "FATAL");
    }
    
    /**
     * ログメッセージにスタックトレースの内容を追加する
     * @param String ログメッセージ
     * @param String スタックトレース文字列
     * @return String 加工済みログメッセージ
     */
    private static function message($msg, $stacktrace = null) {
        // スタックトレースから原因となるエラー箇所のみ抽出
        if ($stacktrace !== null) {
            preg_match('/^#0\s(.*\([0-9]+\)?)/', $stacktrace, $matches);
            $msg .= " - " . $matches[1];
        }
        return $msg;
    }
    
    /**
     * ログを出力する
     * @param String ログメッセージ
     * @param int ログレベル
     * @param String ログレベル文字列
     */
    private static function write($msg, $level, $level_str) {
        // 正規化した絶対パス
        $realpath = Utility::getRoot() . DIRECTORY_SEPARATOR . self::$log_dir;
        if (!realpath($realpath)) {
            throw new Exception("stream log directory does not exist: " . $realpath);
        }
        
        // メッセージを構成
        $msg = "[" . self::getTimeStamp() . "] " . "[" . $level_str . "] " . $msg . "\n";
        
        // ファイルに書き出す
        $log_path = $realpath . "/" . self::$log_filename;
        if ($level >= self::$level) {
            error_log($msg, 3, $log_path);
        }
    }
    
    /**
     * タイムスタンプを取得する
     * @return String タイムスタンプ
     */
    private static function getTimeStamp() {
        $msec = sprintf("%2d", floatval(microtime()) * 100);
        return strftime("%Y-%m-%d %H:%M:%S") . "," . $msec;
    }
}