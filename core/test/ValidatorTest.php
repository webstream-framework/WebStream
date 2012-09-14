<?php
namespace WebStream\Test;
use WebStream\Utility;
use WebStream\Validator;
/**
 * Validatorクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2012/09/13
 */
require_once 'UnitTestBase.php';
 
class ValidatorTest extends UnitTestBase {
    private static $init = false;
    
    public function setUp() {
        parent::setUp();
        if (!self::$init) {
            define('STREAM_CLASSPATH', '\\WebStream\\');
            define('STREAM_APP_DIR', "core/test/testdata/app");
            self::$init = true;
        }
    }
    
    public function tearDown() {}
    
    /**
     * 異常系
     * 指定したコントローラが存在しない場合、例外が発生すること
     * @dataProvider invalidController
     * @expectedException WebStream\ClassNotFoundException
     */
    public function testNgInvalidController($validate_file) {
        \WebStream\import("/core/test/testdata/config/" . $validate_file);
        new Validator();
    }
    
    /**
     * 異常系
     * 指定したアクションが存在しない場合、例外が発生すること
     * @dataProvider invalidAction
     * @expectedException WebStream\MethodNotFoundException
     */
    public function testNgInvalidAction($validate_file) {
        \WebStream\import("/core/test/testdata/config/" . $validate_file);
        new Validator();
    }
    
}