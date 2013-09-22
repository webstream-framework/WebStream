<?php
namespace WebStream\Test;

use WebStream\Annotation\FilterFactory;
use WebStream\Module\Utility;
use WebStream\Module\Logger;

require_once 'TestBase.php';
require_once 'TestConstant.php';

/**
 * FilterFactoryクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/20
 * @version 0.4
 */
class FilterFactoryTest extends TestBase
{
    use Utility, TestConstant;

    public function setUp()
    {
        ob_start();
        parent::setUp();
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
    }

    /**
     * 正常系
     * @Filter("Before")が実行されること
     * @test
     */
    public function okBeforeFilter()
    {
        ob_start();
        $factory = new FilterFactory();
        $object = $factory->create("\WebStream\Test\TestData\BeforeFilterTest1");
        $object->executeAction("index");
        $result = ob_get_clean();
        $this->assertEquals($result, "bi");
    }

    /**
     * 正常系
     * @Filter("Before")が複数実行されること
     * @test
     */
    public function okBeforeFilterMulti()
    {
        ob_start();
        $factory = new FilterFactory();
        $object = $factory->create("\WebStream\Test\TestData\BeforeFilterTest2");
        $object->executeAction("index");
        $result = ob_get_clean();
        $this->assertEquals($result, "b1b2i");
    }

    /**
     * 正常系
     * @Filter("After")が実行されること
     * @test
     */
    public function okAfterFilter()
    {
        ob_start();
        $factory = new FilterFactory();
        $object = $factory->create("\WebStream\Test\TestData\AfterFilterTest1");
        $object->executeAction("index");
        $object = null;
        $result = ob_get_clean();
        $this->assertEquals($result, "ia");
    }

    /**
     * 正常系
     * @Filter("After")が複数実行されること
     * @test
     */
    public function okAfterFilterMulti()
    {
        ob_start();
        $factory = new FilterFactory();
        $object = $factory->create("\WebStream\Test\TestData\AfterFilterTest2");
        $object->executeAction("index");
        $object = null;
        $result = ob_get_clean();
        $this->assertEquals($result, "ia1a2");
    }

    /**
     * 正常系
     * @Filter("Initialize")が実行されること
     * Before filterより前に実行されること
     * @test
     */
    public function okInitializeFilter()
    {
        ob_start();
        $factory = new FilterFactory();
        $object = $factory->create("\WebStream\Test\TestData\InitializeFilterTest1");
        $object->executeAction("index");
        $result = ob_get_clean();
        $this->assertEquals($result, "Iai");
    }

    /**
     * 正常系
     * 親クラスのフィルタを実行出来ること
     * @test
     */
    public function okOverrideMethodFilter()
    {
        ob_start();
        $factory = new FilterFactory();
        $object = $factory->create("\WebStream\Test\TestData\FilterOverrideTest1");
        $object->executeAction("index");
        $result = ob_get_clean();
        $this->assertEquals($result, "a1a2i");
    }

    /**
     * 異常系
     * @Filter("Initialize")が複数定義された場合、例外が発生すること
     * @test
     * @expectedException WebStream\Exception\AnnotationException
     */
    public function ngInitializeFilterMulti()
    {
        $factory = new FilterFactory();
        $object = $factory->create("\WebStream\Test\TestData\InitializeFilterTest2");
    }
}
