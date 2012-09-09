<?php
namespace WebStream\Test;
use WebStream\Annotation;
use WebStream\Utility;
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
        $annotation = new Annotation("WebStream\\TestAnnotation1");
        $list = $annotation->classes("@Database");
        $this->assertNotCount(0, $list);
    }
    
    /**
     * 正常系
     * クラスのアノテーション情報が取得できること
     */
    public function testOkClassAnnotation() {
        $annotation = new Annotation("WebStream\\TestAnnotation1");
        $classAnnotation = $annotation->classes("@Database");
        $this->assertEquals("diarysys", $classAnnotation[0]->value);
        $this->assertEquals("WebStream\\TestAnnotation1", $classAnnotation[0]->className);
    }
    
    /**
     * 正常系
     * メソッドのアノテーション情報が取得できること
     */
    public function testOkMethodAnnotation() {
        $annotation = new Annotation("WebStream\\TestAnnotation3");
        $methodAnnotation = $annotation->methods("@Database");
        $this->assertEquals("diarysys", $methodAnnotation[0]->value);
        $this->assertEquals("testAnnotation", $methodAnnotation[0]->methodName);
    }
    
    /**
     * 正常系
     * クラスのアノテーションの値が複数指定された場合、複数取得できること
     */
    public function testOkClassAnnotations() {
        $annotation = new Annotation("WebStream\\TestAnnotation4");
        $classAnnotations = $annotation->classes("@Hoge");
        $this->assertEquals("users", $classAnnotations[0]->value);
        $this->assertEquals("users2", $classAnnotations[1]->value);
    }
    
    /**
     * 正常系
     * クラスのアノテーションの値が複数指定された場合、複数取得できること
     */
    public function testOkMethodAnnotations() {
        $annotation = new Annotation("WebStream\\TestAnnotation4");
        $methodAnnotations = $annotation->methods("@Fuga");
        $this->assertEquals("foo", $methodAnnotations[0]->value);
        $this->assertEquals("bar", $methodAnnotations[1]->value);
    }
    
    /**
     * 正常系
     * 親クラスのアノテーションの値が取得できること
     */
    public function testOkSuperClassAnnotation() {
        $annotation = new Annotation("WebStream\\TestAnnotation5");
        $classAnnotation = $annotation->classes("@Hoge");
        $this->assertEquals("users", $classAnnotation[0]->value);
        $this->assertEquals("users2", $classAnnotation[1]->value);
    }
    
    /**
     * 正常系
     * 子クラスと親クラスで同じアノテーションが設定された場合、両方取得できること
     */
    public function testOkSuperClassDupulicateAnnotation() {
        $annotation = new Annotation("WebStream\\TestAnnotation6");
        $classAnnotations = $annotation->classes("@Yuruyuri");
        $this->assertEquals("kyouko", $classAnnotations[2]->value);
        $this->assertEquals("yui", $classAnnotations[3]->value);
        $this->assertEquals("akari", $classAnnotations[0]->value);
        $this->assertEquals("chinachu", $classAnnotations[1]->value);
    }
    
    /**
     * 正常系
     * 親クラスのメソッドのアノテーションの値が取得できること
     */
    public function testOkSuperClassMethodAnnotation() {
        $annotation = new Annotation("WebStream\\TestAnnotation8");
        $methodAnnotation = $annotation->methods("@Yuri");
        $this->assertEquals("toshinou", $methodAnnotation[0]->value);
    }
    
    /**
     * 正常系
     * 子クラスのメソッドと親クラスのメソッドで同じアノテーションが設定された場合、両方取得できること
     */
    public function testOkSuperClassMethodAnnotations() {
        $annotation = new Annotation("WebStream\\TestAnnotation9");
        $methodAnnotations = $annotation->methods("@Yuri");
        $this->assertEquals("sugiura", $methodAnnotations[0]->value);
        $this->assertEquals("toshinou", $methodAnnotations[1]->value);
    }
    
    /**
     * 正常系
     * 親クラスと同名のメソッドがあった場合、アノテーションの値が取得できること
     */
    public function testOkSuperClassDupulicateMethodAnnotation() {
        $annotation = new Annotation("WebStream\\TestAnnotation11");
        $methodAnnotations = $annotation->methods("@Yuruyuri");
        $this->assertEquals("sakurako", $methodAnnotations[0]->value);
        $this->assertEquals("himawari", $methodAnnotations[1]->value);
    }
    
    /**
     * 異常系
     * @Injectが付いていない場合、アノテーション情報が取得できないこと
     */
    public function testNgGetInjectAnnotation() {
        $annotation = new Annotation("WebStream\\TestAnnotation2");
        $list = $annotation->classes("@Database");
        $this->assertCount(0, $list);
    }
}