<?php
namespace WebStream\Test;

use WebStream\Annotation\Type;
use WebStream\Annotation\AutowiredReader;
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
        $reader = new AutowiredReader();
        $container = ServiceLocator::getContainer();
        $refClass = new \ReflectionClass("\WebStream\Test\TestData\AutowiredTest1");
        $reader->read($refClass, null, $container);
        $receiver = $reader->getReceiver();
        $this->assertEquals($mail, $receiver->getMail());
        $this->assertEquals($age, $receiver->getAge());
    }

    /**
     * 正常系
     * @Valueで指定した値(定数型)を注入できること
     * @test
     * @dataProvider autowiredForConstantValueProvider
     */
    public function okAutowiredForConstantValueProvider($name, $num)
    {
        $reader = new AutowiredReader();
        $container = ServiceLocator::getContainer();
        $refClass = new \ReflectionClass("\WebStream\Test\TestData\AutowiredTest3");
        $reader->read($refClass, null, $container);
        $receiver = $reader->getReceiver();
        $this->assertEquals($name, $receiver->getName());
        $this->assertEquals($num, $receiver->getMemberNum());
    }

    /**
     * 正常系
     * @Autowiredと@Type,@Valueの順序が逆でもインスタンスを注入できること
     * @test
     * @dataProvider autowiredProvider
     */
    public function okAutowiredReverse($instance, $mail, $age)
    {
        $reader = new AutowiredReader();
        $container = ServiceLocator::getContainer();
        $refClass = new \ReflectionClass("\WebStream\Test\TestData\AutowiredTest4");
        $reader->read($refClass, null, $container);
        $receiver = $reader->getReceiver();
        $this->assertEquals($mail, $receiver->getMail());
        $this->assertEquals($age, $receiver->getAge());
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
        $reader = new AutowiredReader();
        $container = ServiceLocator::getContainer();
        $refClass = new \ReflectionClass("\WebStream\Test\TestData\AutowiredTest2");
        $reader->read($refClass, null, $container);
    }

    /**
     * 異常系
     * 間違ったアノテーション定義をした場合、例外が発生すること
     * @test
     * @expectedException Doctrine\Common\Annotations\AnnotationException
     */
    public function ngAutowiredAnnotationDefinition()
    {
        $reader = new AutowiredReader();
        $container = ServiceLocator::getContainer();
        $refClass = new \ReflectionClass("\WebStream\Test\TestData\AutowiredTest6");
        $reader->read($refClass, null, $container);
    }
}
