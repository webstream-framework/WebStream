<?php
/**
 * Annotationクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2012/08/30
 */
require_once 'UnitTestBase.php';
//require_once Utility::getRoot() . ""

class AnnotationTest extends UnitTestBase {
    
    public function setUp() {
        parent::setUp();
    }
    
    public function tearDown() {
        
    }
    
    
    public function testOkGetInjectAnnotation() {
        require_once Utility::getRoot() . "/core/test/testdata/app/models/TestAnnotation.php";
        $annotation = new Annotation("TestAnnotation");
        $list = $annotation->classes("@Database");
        var_dump($list);
        $list = $annotation->classes("@SQL");
        var_dump($list);
        $list = $annotation->methods("@Hogehoge");
        var_dump($list);
        
        $this->assertTrue(true);
    }
}
