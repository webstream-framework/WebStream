<?php
namespace WebStream\Test;

use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Annotation\Reader\AutowiredReader;
use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\DI\ServiceLocator;
use WebStream\Test\DataProvider\AutowiredProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/AutowiredProvider.php';

/**
 * Autowiredクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/17
 * @version 0.4
 */
class AutowiredTest extends TestBase
{
    use Utility, TestConstant, AutowiredProvider;

    private $reader;

    public function setUp()
    {
        parent::setUp();
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
    }

    /**
     * 正常系
     * @Type,@Valueで指定した値が注入できること
     * @test
     * @dataProvider autowiredProvider
     */
    public function okAutowired($mail, $age)
    {
        $instance = new \WebStream\Test\TestData\AutowiredTest1();
        $reader = new AnnotationReader($instance);
        $container = ServiceLocator::getContainer();
        $reader->setContainer($container);
        $reader->read();

        $autowired = new AutowiredReader($reader);
        $autowired->inject($instance);
        $autowired->execute();
        $instance = $autowired->getInstance();

        $this->assertEquals($mail, $instance->getMail());
        $this->assertEquals($age, $instance->getAge());
    }

    /**
     * 正常系
     * @Valueで指定した値(定数型)を注入できること
     * @test
     * @dataProvider autowiredForConstantValueProvider
     */
    public function okAutowiredForConstantValueProvider($name, $num)
    {
        $instance = new \WebStream\Test\TestData\AutowiredTest3();
        $reader = new AnnotationReader($instance);
        $container = ServiceLocator::getContainer();
        $reader->setContainer($container);
        $reader->read();

        $autowired = new AutowiredReader($reader);
        $autowired->inject($instance);
        $autowired->execute();
        $instance = $autowired->getInstance();

        $this->assertEquals($name, $instance->getName());
        $this->assertEquals($num, $instance->getMemberNum());
    }

    /**
     * 正常系
     * オーバライドしているメソッドに対してもAutowiredできること
     * @test
     */
    public function okAutowiredSuperClass()
    {
        $instance = new \WebStream\Test\TestData\AutowiredTest7();
        $reader = new AnnotationReader($instance);
        $container = ServiceLocator::getContainer();
        $reader->setContainer($container);
        $reader->read();

        $autowired = new AutowiredReader($reader);
        $autowired->inject($instance);
        $autowired->execute();
        $instance = $autowired->getInstance();

        $autowired1 = $instance->getName();
        $autowired2 = $instance->getName2();
        $autowired3 = $instance->getName3();
        $autowired4 = $instance->getName4();
        $this->assertEquals($autowired1, "default1");
        $this->assertEquals($autowired2, "name2");
        $this->assertEquals($autowired3, "default3");
        $this->assertEquals($autowired4, "name4");
    }

    /**
     * 異常系
     * valueに存在しないクラス参照型を指定した場合、例外が発生すること
     * @test
     * @expectedException WebStream\Exception\Extend\AnnotationException
     */
    public function ngAutowiredInvalidType()
    {
        $instance = new \WebStream\Test\TestData\AutowiredTest2();
        $reader = new AnnotationReader($instance);
        $container = ServiceLocator::getContainer();
        $reader->setContainer($container);
        $reader->read();

        $autowired = new AutowiredReader($reader);
        $autowired->inject($instance);
        $autowired->execute();
        $instance = $autowired->getInstance();

        $this->assertTrue(false);
    }

    /**
     * 異常系
     * 間違ったアノテーション定義をした場合、値はセットされないこと
     * @test
     */
    public function ngAutowiredAnnotationDefinition()
    {
        $instance = new \WebStream\Test\TestData\AutowiredTest6();
        $reader = new AnnotationReader($instance);
        $container = ServiceLocator::getContainer();
        $reader->setContainer($container);
        $reader->read();

        $autowired = new AutowiredReader($reader);
        $autowired->inject($instance);
        $autowired->execute();
        $instance = $autowired->getInstance();

        $this->assertNull($instance->getInstance());
    }
}
