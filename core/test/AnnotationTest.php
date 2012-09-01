<?php
/**
 * Annotationクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2012/08/30
 */
require_once 'UnitTestBase.php';
class AnnotationTest extends UnitTestBase {
    public function setUp() {
        parent::setUp();
        require_once Utility::getRoot() . "/core/test/testdata/app/models/TestAnnotation.php";
    }
    
    /**
     * 正常系
     * @Injectが付いている場合、アノテーション情報が取得できること
     */
    public function testOkGetInjectAnnotation() {
        $annotation = new Annotation("TestAnnotation1");
        $list = $annotation->classes("@Database");
        $this->assertNotCount(0, $list);
    }
    
    /**
     * 正常系
     * クラスのアノテーション情報が取得できること
     */
    public function testOkClassAnnotation() {
        $annotation = new Annotation("TestAnnotation1");
        $classAnnotation = $annotation->classes("@Database");
        $this->assertEquals("diarysys", $classAnnotation[0]->value);
        $this->assertEquals("TestAnnotation1", $classAnnotation[0]->name);
    }
    
    /**
     * 正常系
     * メソッドのアノテーション情報が取得できること
     */
    public function testOkMethodAnnotation() {
        $annotation = new Annotation("TestAnnotation3");
        $methodAnnotation = $annotation->methods("@Database");
        $this->assertEquals("diarysys", $methodAnnotation[0]->value);
        $this->assertEquals("testAnnotation", $methodAnnotation[0]->name);
    }
    
    /**
     * 異常系
     * @Injectが付いていない場合、アノテーション情報が取得できないこと
     */
    public function testNgGetInjectAnnotation() {
        $annotation = new Annotation("TestAnnotation2");
        $list = $annotation->classes("@Database");
        $this->assertCount(0, $list);
    }
    
    
}
