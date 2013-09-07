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
     * coreディレクトリクラスがロード出来ること
     * @test
     */
    public function okLoadClass()
    {
        $instance = new \WebStream\Test\TestData\ClassLoaderTestClass();
        $this->assertEquals($instance->getName(), "hoge");
    }

    /**
     * 正常系
     * coreディレクトリトレイトがロード出来ること
     * @test
     */
    public function okLoadTrait()
    {
        $instance = new \WebStream\Test\TestData\ClassLoaderTestTraitClass();
        $this->assertEquals($instance->getName(), "hoge");
    }

    /**
     * 正常系
     * appディレクトリクラスがロード出来ること
     * @test
     */
    public function okLoadModuleWithoutNamespace()
    {
        new \WebStream\SampleLibrary();
        $this->assertTrue(true);
    }
}
