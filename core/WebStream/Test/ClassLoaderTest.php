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
    public function okLoadModule()
    {
        new \WebStream\Test\TestData\ClassLoaderTestData();
        $this->assertTrue(true);
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
