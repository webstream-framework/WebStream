<?php
namespace WebStream\Test;

use WebStream\Module\ClassLoader;
use WebStream\Module\Logger;

require_once 'TestBase.php';
require_once 'TestConstant.php';

/**
 * ClassLoaderクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2013/09/02
 * @version 0.4
 */
class ClassLoaderTest extends TestBase
{
    use TestConstant;

    public function setUp()
    {
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
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
        new \WebStream\Test\TestData\Sample\App\Library\SampleLibrary();
        $this->assertTrue(true);
    }

    /**
     * 正常系
     * クラスを静的にロード出来ること
     * @test
     */
    public function okLoadClass()
    {
        $classLoader = new ClassLoader();
        $classLoader->test();
        $classLoader->load("ClassLoaderTestClassStaticLoad");
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
        $classLoader = new ClassLoader();
        $classLoader->load(["ClassLoaderTestClassStaticLoadMultiple1", "ClassLoaderTestClassStaticLoadMultiple2"]);
        $classLoader->test();
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
        $classLoader = new ClassLoader();
        $classLoader->test();
        $classLoader->import("core/WebStream/Test/TestData/ClassLoaderTestImport.php");
        $this->assertTrue(function_exists("testImport"));
    }

    /**
     * 正常系
     * クラス以外のファイルを全てインポートできること
     * @test
     */
    public function okImportAllFile()
    {
        $classLoader = new ClassLoader();
        $classLoader->test();
        $classLoader->importAll("core/WebStream/Test/TestData/ClassLoaderTest");
        $this->assertTrue(function_exists("testImportAll1"));
        $this->assertTrue(function_exists("testImportAll2"));
    }

    /**
     * 正常系
     * 検索結果で複数のファイルが該当した場合、全てロードされること
     * @test
     */
    public function okSearchMultipleFile()
    {
        $classLoader = new ClassLoader();
        $classLoader->test();
        $classLoader->load("UtilityFileSearch");
        $instance1 = new \WebStream\Test\TestData\UtilityFileSearch1();
        $instance2 = new \WebStream\Test\TestData\UtilityFileSearch2();
        $this->assertTrue($instance1 instanceof \WebStream\Test\TestData\UtilityFileSearch1);
        $this->assertTrue($instance2 instanceof \WebStream\Test\TestData\UtilityFileSearch2);
    }

    /**
     * 異常系
     * 複数のクラスロード時に存在しないクラスが指定された場合、
     * 存在するクラスもロードされないこと
     * @test
     */
    public function ngLoadMultipleClass()
    {
        $classLoader = new ClassLoader();
        $classLoader->test();
        $classLoader->load(["ClassLoaderTestClassStaticLoadMultiple3", "DummyClass"]);
        $instance = new \WebStream\Test\TestData\ClassLoaderTestClassStaticLoadMultiple3();
    }
}
