<?php
namespace WebStream;
/**
 * 各種Exceptionクラス
 * @author Ryuichi Tanaka
 * @since 2011/09/29
 */
/** CSRFエラー例外 */
class CsrfException extends \Exception {}
/** セッション例外 */
class SessionTimeoutException extends \Exception {}
/** ルーティング失敗例外 */
class RouterException extends \Exception {}
/** バリデーション例外 */
class ValidatorException extends \Exception {}
/** リソース取得失敗例外 */
class ResourceNotFoundException extends \Exception {}
/** クラスロード失敗例外 */
class ClassNotFoundException extends \Exception {}
/** メソッドロード失敗例外 */
class MethodNotFoundException extends \Exception {}
/** テンプレート取得失敗例外 */
class TemplateNotFoundException extends \Exception {}
/** データベース例外 */
class DatabaseException extends \Exception {}
/** ロガー例外 */
class LoggerException extends \Exception {}
/** 通信例外 */
class ConnectionException extends \Exception {}
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
/** アノテーション例外 */
class AnnotationException extends \Exception {}