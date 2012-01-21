<?php
/**
 * Loggerクラス
 * @author Ryuichi Tanaka
 * @since 2012/01/16
 */
class Logger {
    /** ログレベル(数値) */
    private static $level_value = 0;
    /** ログパス */
    private static $path;
    
    /**
     * コンストラクタ
     */
    private function __construct() {}
    
    /**
     * Loggerを初期化する
     * @param String 設定ファイルパス
     */
    public static function init($config_path = "config/log.ini") {
        self::loadCofig($config_path);
    }
    
    /**
     * Loggerメソッドの呼び出しを受ける
     * @param String メソッド名(ログレベル文字列)
     * @param Array 引数
     */
    public static function __callStatic($level, $arguments) {
        if (self::level2value($level) >= self::$level_value) {
            $logger = new Logger();
            $log_arguments = array(self::$path, strtoupper($level));
            call_user_func_array(array($logger, "write"), array_merge($log_arguments, $arguments));
        }
    }
    
    /**
     * 設定ファイルを読み込む
     */
    private static function loadCofig($config_path) {
        $log = Utility::parseConfig($config_path);
        // 設定ファイルが存在するかどうか
        if ($log === null) {
            throw new LoggerException("Log config file does not exist: " . $config_path);
        }
        
        $level_value = self::level2value($log["level"]);
        // 妥当なログレベルかどうか
        if ($level_value === 0) {
            throw new LoggerException("Invalid log level: " . $log["level"]);
        }
        self::$level_value = $level_value;
        
        $path = realpath(Utility::getRoot() . "/" . $log["path"]);
        // ディレクトリが存在するパスかどうか
        if (!$path) {
            throw new LoggerException("Log directory does not exist: " . $log["path"]);
        }
        self::$path = $path;
    }
    
    /**
     * ログレベルを数値に変換
     * @param String ログレベル文字列
     * @return int ログレベル数値
     */
    private static function level2value($level) {
        switch (strtolower($level)) {
            case 'debug': return 1;
            case 'info' : return 2;
            case 'warn' : return 3;
            case 'error': return 4;
            case 'fatal': return 5;
            default: return 0;
        }
    }
    
    /**
     * タイムスタンプを取得する
     * @return String タイムスタンプ
     */
    private function getTimeStamp() {
        $msec = sprintf("%2d", floatval(microtime()) * 100);
        return strftime("%Y-%m-%d %H:%M:%S") . "," . $msec;
    }
    
    /**
     * ログメッセージにスタックトレースの内容を追加する
     * @param String ログメッセージ
     * @param String スタックトレース文字列
     * @return String 加工済みログメッセージ
     */
    private function message($msg, $stacktrace = null) {
        // スタックトレースから原因となるエラー箇所のみ抽出
        if ($stacktrace !== null) {
            preg_match('/^#0\s(.*\([0-9]+\)?)/', $stacktrace, $matches);
            $msg .= " - " . $matches[1];
        }
        return $msg;
    }
    
    /**
     * ログを書き出す
     * @param String ログパス
     * @param String ログレベル文字列
     * @param String 書きだす文字列
     * @param String スタックトレース文字列
     */
    private function write($path, $level, $msg, $stacktrace = null) {
        $msg = $this->message($msg, $stacktrace);
        $msg = "[".$this->getTimeStamp()."] [".$level."] ".$msg."\n";
        error_log($msg, 3, $path);
    }
}

class LoggerException extends Exception {}
