<?php
namespace WebStream;
/**
 * Loggerクラス
 * @author Ryuichi Tanaka
 * @since 2012/01/16
 */
class Logger {
    /** ログレベル(数値) */
    private static $level_value = 0;
    /** ログパス */
    private $_path;
    private static $path;
    /** ステータスファイル */
    private $_status_file;
    /** ログローテート設定 */
    private $_rotate_cycle;
    private $_rotate_size;
    private static $rotate_cycle;
    private static $rotate_size;
    
    /**
     * コンストラクタ
     * @param String ログファイルパス
     * @param String ローテートサイクル
     * @param String ローテートサイズ
     */
    private function __construct($path, $rotate_cycle, $rotate_size) {
        $this->_path = $path;
        $this->_rotate_cycle = $rotate_cycle;
        $this->_rotate_size = $rotate_size;
        $this->_status_file = preg_replace_callback('/(.*)\..+/', function($matches) {
            return "$matches[1].status";
        }, $this->_path);
    }
    
    /**
     * Loggerを初期化する
     * @param String 設定ファイルパス
     */
    public static function init($config_path = "config/log.ini") {
        self::$path = null;
        self::$rotate_cycle = null;
        self::$rotate_size = null;
        self::loadCofig($config_path);
    }
    
    /**
     * Loggerメソッドの呼び出しを受ける
     * @param String メソッド名(ログレベル文字列)
     * @param Array 引数
     */
    public static function __callStatic($level, $arguments) {
        if (self::level2value($level) >= self::$level_value) {
            $logger = new Logger(self::$path, self::$rotate_cycle, self::$rotate_size);
            $log_arguments = array(strtoupper($level));
            call_user_func_array(array($logger, "write"), array_merge($log_arguments, $arguments));
        }
    }
    
    /**
     * 設定ファイルを読み込む
     * @param String 設定ファイルパス
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
        
        $path = $log["path"];
        // 絶対パスでのチェック
        if (!realpath(dirname($path))) {
            $path = Utility::getRoot() . "/" . $log["path"];
            // プロジェクトルートからの相対パスでのチェック
            if (!realpath(dirname($path))) {
                throw new LoggerException("Log directory does not exist: " . $log["path"]);
            }
        }
        self::$path = $path;
        
        if (isset($log["rotate_cycle"])) {
            $rotate_cycle = self::cycle2value($log["rotate_cycle"]);
            // 妥当なローテートサイクルか
            if ($rotate_cycle === 0) {
                throw new LoggerException("Invalid log rotate cycle: " . $log["rotate_cycle"]);
            }
            self::$rotate_cycle = $rotate_cycle;
        }
        if (isset($log["rotate_size"])) {
            $rotate_size = intval($log["rotate_size"]);
            // ローテートサイズが不正の場合(正の整数以外の値が設定された場合)
            if ($rotate_size <= 0) {
                throw new LoggerException("Invalid log rotate size: " . $log["rotate_size"]);
            }
            self::$rotate_size = $rotate_size;
        }
    }
    
    /**
     * ログレベルを数値に変換
     * @param String ログレベル文字列
     * @return Integer ログレベル数値
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
     * ログローテートサイクルを時間に変換
     * @param String ローテートサイクル
     */
    private static function cycle2value($cycle) {
        $day_to_h = 24;
        $week_to_h = $day_to_h * 7;
        $month_to_h = $day_to_h * intval(date("t", time()));
        $year_to_h = $day_to_h * 365;
        
        $year = date("Y");
        if (($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0) {
            $year_to_h = $day_to_h * 366;
        }
        
        switch (strtolower($cycle)) {
            case 'day'  : return $day_to_h;
            case 'week' : return $week_to_h;
            case 'month': return $month_to_h;
            case 'year' : return $year_to_h;
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
        $stacktrace_list = explode("#", $stacktrace);
        foreach ($stacktrace_list as $stacktrace_line) {
            if ($stacktrace_line === "") continue;
            $msg .= "\n";
            $msg .= "\t#" . trim($stacktrace_line);
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
    private function write($level, $msg, $stacktrace = null) {
        $msg = $this->message($msg, $stacktrace);
        $msg = "[".$this->getTimeStamp()."] [".$level."] ".$msg."\n";
        $this->rotate();
        try {
            error_log($msg, 3, $this->_path);
        }
        catch (\Exception $e) {
            throw new LoggerException($e->getMessage());
        }
    }
    
    /**
     * ログステータスファイルに書きこむ
     */
    private function writeStatus() {
        file_put_contents($this->_status_file, intval(preg_replace('/^.*\s/', '', microtime())));
    }
    
    /**
     * ログステータスファイルを読み込む
     */
    private function readStatus() {
        $handle = fopen($this->_status_file, "r");
        $size = filesize($this->_status_file);
        $content = fread($handle, $size);
        fclose($handle);
        if (!preg_match('/^\d{10}$/', $content)) {
            throw new LoggerException("Invalid log state file contents: " . $content);
        }
        return intval($content);
    }
    
    /**
     * ステータスファイルを作成する
     */
    private function createStatusFile() {
        // ステータスファイルがない場合は書きだす
        if (!is_file($this->_status_file)) {
            $this->writeStatus();
        }
    }
    
    /**
     * ログファイルをアーカイブする
     * stream.log -> stream.(作成日時)-(現在日時).log
     * @param String ログファイルパス
     */
    private function rotate() {
        // ログファイルがない場合はローテートしない
        if (!realpath($this->_path)) return;
        // ログローテート実行
        if ($this->_rotate_cycle !== null) {
            $this->rotateByCycle();
        }
        else if ($this->_rotate_size !== null) {
            $this->rotateBySize();
        }
    }
    
    /**
     * ローテートを実行する
     * @param Integer 作成日時のUnixTime
     * @param Integer 現在日時のUnixTime
     */
    private function runRotate($from, $to) {
        $from_date = date("YmdHis", $from);
        $to_date = date("YmdHis", $to);
        $archive_path = null;
        if (preg_match('/(.*)\.(.+)/', $this->_path, $matches)) {
            $archive_path = "$matches[1].${from_date}-${to_date}.$matches[2]";
            // mvを実行
            rename($this->_path, $archive_path);
            // ステータスファイルを削除
            unlink($this->_status_file);
        }
    }

    /**
     * 時間単位でローテートする
     * stream.log -> stream.(作成日時)-(現在日時).log
     */
    private function rotateByCycle() {
        $this->createStatusFile();
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $this->readStatus($this->_status_file);
        
        // ローテート周期を過ぎている場合はログファイルをアーカイブする
        $hour = intval(floor(($now - $created_at) / 3600));
        if ($hour >= $this->_rotate_cycle) {
            $this->runRotate($created_at, $now);
        }
    }
    
    /**
     * サイズ単位でローテートする
     * stream.log -> stream.(作成日時)-(現在日時).log
     */
    private function rotateBySize() {
        $this->createStatusFile();
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $this->readStatus();
        
        $size_kb = (int) floor(filesize($this->_path) / 1024);
        // 指定したサイズより大きければローテート
        if ($size_kb >= $this->_rotate_size) {
            $this->runRotate($created_at, $now);
        }
    }
}
