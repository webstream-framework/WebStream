<?php
namespace WebStream\Test;

use WebStream\Annotation\Type;
use WebStream\Annotation\AutowiredFactory;
use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Test\DataProvider\AutowiredFactoryProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/AutowiredFactoryProvider.php';

/**
 * AutowiredFactoryクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/17
 * @version 0.4
 */
class AutowiredFactoryTest extends TestBase
{
    use Utility, TestConstant, AutowiredFactoryProvider;

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
    public function okAutowired($instance, $mail, $age)
    {
        $factory = new AutowiredFactory();
        $object = $factory->create("\WebStream\Test\TestData\AutowiredTest1");
        $this->assertInstanceOf("\WebStream\Test\TestData\AutowiredTest1", $object);
        $this->assertInstanceOf($instance, $object->getInstance());
        $this->assertEquals($mail, $object->getMail());
        $this->assertEquals($age, $object->getAge());
    }

    /**
     * 正常系
     * @Valueで指定した値(定数型)を注入できること
     * @test
     * @dataProvider autowiredForConstantValueProvider
     */
    public function okAutowiredForConstantValueProvider($name, $num)
    {
        $factory = new AutowiredFactory();
        $object = $factory->create("\WebStream\Test\TestData\AutowiredTest3");
        $this->assertInstanceOf("\WebStream\Test\TestData\AutowiredTest3", $object);
        $this->assertEquals($name, $object->getName());
        $this->assertEquals($num, $object->getMemberNum());
    }

    /**
     * 正常系
     * @Autowiredと@Type,@Valueの順序が逆でもインスタンスを注入できること
     * @test
     * @dataProvider autowiredProvider
     */
    public function okAutowiredReverse($instance, $mail, $age)
    {
        $factory = new AutowiredFactory();
        $object = $factory->create("\WebStream\Test\TestData\AutowiredTest4");
        $this->assertInstanceOf("\WebStream\Test\TestData\AutowiredTest4", $object);
        $this->assertInstanceOf($instance, $object->getInstance());
        $this->assertEquals($mail, $object->getMail());
        $this->assertEquals($age, $object->getAge());
    }

    /**
     * 異常系
     * @Typeで指定したクラスが存在しないまたはrequireされていない場合、
     * 例外が発生すること
     * @test
     * @expectedException WebStream\Exception\AnnotationException
     */
    public function ngAutowiredInvalidType()
    {
        $factory = new AutowiredFactory();
        $factory->create("\WebStream\Test\TestData\AutowiredTest2");
    }

    /**
     * 異常系
     * 間違ったアノテーション定義をした場合、例外が発生すること
     * @test
     * @expectedException WebStream\Exception\AnnotationException
     */
    public function ngAutowiredAnnotationDefinition()
    {
        $factory = new AutowiredFactory();
        $factory->create("\WebStream\Test\TestData\AutowiredTest6");
    }
}
