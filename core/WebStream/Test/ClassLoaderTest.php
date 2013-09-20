<?php
namespace WebStream\Test;

use WebStream\Module\ClassLoader;

require_once 'TestBase.php';

/**
 * ClassLoaderクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2013/09/02
 * @version 0.4
 */
class ClassLoaderTest extends TestBase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * 正常系
     * coreディレクトリクラスがオートロード出来ること
     * @test
     */
    public function okAutoLoadClass()
    {
        $instance = new \WebStream\Test\TestData\ClassLoaderTestClass();
        $this->assertEquals($instance->getName(), "hoge");
    }

    /**
     * 正常系
     * coreディレクトリトレイトがオートロード出来ること
     * @test
     */
    public function okAutoLoadTrait()
    {
        $instance = new \WebStream\Test\TestData\ClassLoaderTestTraitClass();
        $this->assertEquals($instance->getName(), "hoge");
    }

    /**
     * 正常系
     * appディレクトリクラスがオートロード出来ること
     * @test
     */
    public function okAutoLoadModuleWithoutNamespace()
    {
        new \WebStream\SampleLibrary();
        $this->assertTrue(true);
    }

    /**
     * 正常系
     * クラスを静的にロード出来ること
     * @test
     */
    public function okLoadClass()
    {
        $classLoader = new \WebStream\Module\ClassLoader();
        $isLoad = $classLoader->load("ClassLoaderTestClassStaticLoad");
        $this->assertTrue($isLoad);
        $instance = new \WebStream\Test\TestData\ClassLoaderTestClassStaticLoad();
        $this->assertTrue($instance instanceof \WebStream\Test\TestData\ClassLoaderTestClassStaticLoad);
    }

    /**
     * 正常系
     * クラスを静的に複数ロード出来ること
     * @test
     */
    public function okLoadMultipleClass()
    {
        $classLoader = new \WebStream\Module\ClassLoader();
        $isLoad = $classLoader->load(["ClassLoaderTestClassStaticLoadMultiple1", "ClassLoaderTestClassStaticLoadMultiple2"]);
        $this->assertTrue($isLoad);
        $instance1 = new \WebStream\Test\TestData\ClassLoaderTestClassStaticLoadMultiple1();
        $instance2 = new \WebStream\Test\TestData\ClassLoaderTestClassStaticLoadMultiple2();
        $this->assertTrue($instance1 instanceof \WebStream\Test\TestData\ClassLoaderTestClassStaticLoadMultiple1);
        $this->assertTrue($instance2 instanceof \WebStream\Test\TestData\ClassLoaderTestClassStaticLoadMultiple2);
    }

    /**
     * 正常系
     * クラス以外のファイルをインポートできること
     * @test
     */
    public function okImportFile()
    {
        $classLoader = new \WebStream\Module\ClassLoader();
        $isLoad = $classLoader->import("core/WebStream/Test/TestData/ClassLoaderTestImport.php");
        $this->assertTrue($isLoad);
        $this->assertTrue(function_exists("testImport"));
    }

    /**
     * 異常系
     * 存在しないクラスはロードできないこと
     * @test
     */
    public function ngLoadClass()
    {
        $classLoader = new \WebStream\Module\ClassLoader();
        $isLoad = $classLoader->load("DummyClass");
        $this->assertFalse($isLoad);
    }

    /**
     * 異常系
     * 複数のクラスロード時に存在しないクラスが指定された場合、
     * 存在するクラスもロードされないこと
     * @test
     */
    public function ngLoadMultipleClass()
    {
        $classLoader = new \WebStream\Module\ClassLoader();
        $isLoad = $classLoader->load(["ClassLoaderTestClassStaticLoadMultiple3", "DummyClass"]);
        $this->assertFalse($isLoad);
        $instance = new \WebStream\Test\TestData\ClassLoaderTestClassStaticLoadMultiple3();
    }
}
