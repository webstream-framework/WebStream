<?php
/**
 * 各種Exceptionクラス
 * @author Ryuichi Tanaka
 * @since 2011/09/29
 */
/** CSRFエラー例外 */
class CsrfException extends Exception {}
/** ルーティング失敗例外 */
class RouterException extends Exception {}
/** リソース取得失敗例外 */
class ResourceNotFoundException extends Exception {}
/** クラスロード失敗例外 */
class ClassNotFoundException extends Exception {}
/** メソッドロード失敗例外 */
class MethodNotFoundException extends Exception {}
/** データベース例外 */
class DatabaseException extends Exception {}
class DatabasePropertiesException extends DatabaseException {}
/** Service,Modelロード失敗例外 */
class ServiceModelClassNotFoundException {
    private $msg;
        
    public function __construct($msg) {
        $this->msg = $msg;
    }
    
    public function __call($method, $arguments) {
        throw new ClassNotFoundException($this->msg);
    }
}
