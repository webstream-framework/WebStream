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
     * @Typeで指定した型のインスタンスを注入できること
     * @test
     */
    public function okAutowiredForType()
    {
        $factory = new AutowiredFactory();
        $instances = $factory->create("\WebStream\Test\TestData\AutowiredTest1");
        $this->assertInstanceOf("\WebStream\Annotation\Type", $instances["name"]);
        $this->assertInstanceOf("\WebStream\Test\TestData\AutowiredTestType", $instances["name"]->getInstance());
    }

    /**
     * 正常系
     * @Valueで指定した値(プリミティブ型)を注入できること
     * @test
     * @dataProvider autowiredForValueProvider
     */
    public function okAutowiredForValue($propertyName, $value)
    {
        $factory = new AutowiredFactory();
        $instances = $factory->create("\WebStream\Test\TestData\AutowiredTest1");
        $this->assertEquals($value, $instances[$propertyName]->getValue());
    }

    /**
     * 正常系
     * @Valueで指定した値(定数型)を注入できること
     * @test
     * @dataProvider autowiredForConstantValueProvider
     */
    public function okAutowiredForConstantValueProvider($propertyName, $value)
    {
        $factory = new AutowiredFactory();
        $instances = $factory->create("\WebStream\Test\TestData\AutowiredTest3");
        $this->assertEquals($value, $instances[$propertyName]->getValue());
    }

    /**
     * 正常系
     * @Autowiredと@Typeの順序が逆でもインスタンスを注入できること
     * @test
     */
    public function okAutowiredForTypeReverse()
    {
        $factory = new AutowiredFactory();
        $instances = $factory->create("\WebStream\Test\TestData\AutowiredTest4");
        $this->assertInstanceOf("\WebStream\Annotation\Type", $instances["name"]);
        $this->assertInstanceOf("\WebStream\Test\TestData\AutowiredTestType", $instances["name"]->getInstance());
    }

    /**
     * 正常系
     * @Valueで指定した値(プリミティブ型)を注入できること
     * @test
     * @dataProvider autowiredForValueProvider
     */
    public function okAutowiredForValueReverse($propertyName, $value)
    {
        $factory = new AutowiredFactory();
        $instances = $factory->create("\WebStream\Test\TestData\AutowiredTest4");
        $this->assertEquals($value, $instances[$propertyName]->getValue());
    }

    /**
     * 異常系
     * @Typeで指定したクラスが存在しないまたはrequireされていない場合、
     * 例外が発生すること
     * @test
     * @dataProvider autowiredInvalidTypeProvider
     * @expectedException WebStream\Exception\AnnotationException
     */
    public function ngAutowiredInvalidType($classpath)
    {
        $factory = new AutowiredFactory();
        $factory->create($classpath);
    }
}
