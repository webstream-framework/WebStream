<?php
namespace WebStream\Test;

use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Module\HttpClient;
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
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
        parent::setUp();
    }

    /**
     * 正常系
     * 指定した値が注入できること
     * @test
     * @dataProvider autowiredProvider
     */
    public function okAutowired($mail, $age)
    {
        ServiceLocator::test();
        $container = ServiceLocator::getContainer();
        $container->executeMethod = "";
        $instance = new \WebStream\Test\TestData\AutowiredTest1($container);
        $reader = new AnnotationReader($instance, $container);
        $reader->read();

        $this->assertEquals($mail, $instance->getMail());
        $this->assertEquals($age, $instance->getAge());
    }

    /**
     * 正常系
     * value属性で指定した値(定数型)を注入できること
     * @test
     * @dataProvider autowiredForConstantValueProvider
     */
    public function okAutowiredForConstantValueProvider($name, $num)
    {
        ServiceLocator::test();
        $container = ServiceLocator::getContainer();
        $container->executeMethod = "";
        $instance = new \WebStream\Test\TestData\AutowiredTest3($container);
        $reader = new AnnotationReader($instance, $container);
        $reader->read();

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
        ServiceLocator::test();
        $container = ServiceLocator::getContainer();
        $container->executeMethod = "";
        $instance = new \WebStream\Test\TestData\AutowiredTest7($container);
        $reader = new AnnotationReader($instance, $container);
        $reader->read();

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
     * 正常系
     * 各レイヤ(Controller/Service/Model/Helper)でAutowiredが有効になること
     * @dataProvider autowiredMVCLayerProvider
     * @test
     */
    public function okAutowiredMVCLayer($path, $response)
    {
        $http = new HttpClient();
        $result = $http->get($this->getDocumentRootURL() . $path);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($response, $result);
    }

    /**
     * 異常系
     * value属性に存在しないクラス参照型を指定した場合、例外が発生すること
     * @test
     * @expectedException WebStream\Exception\Extend\AnnotationException
     */
    public function ngAutowiredInvalidType()
    {
        ServiceLocator::test();
        $container = ServiceLocator::getContainer();
        $container->executeMethod = "";
        $instance = new \WebStream\Test\TestData\AutowiredTest2($container);
        $reader = new AnnotationReader($instance, $container);
        $reader->read();
        $exception = $reader->getException();

        $this->assertTrue(is_callable($exception));
        $exception();
    }

    /**
     * 異常系
     * 間違ったアノテーション定義をした場合、値はセットされないこと
     * @test
     */
    public function ngAutowiredAnnotationDefinition()
    {
        ServiceLocator::test();
        $container = ServiceLocator::getContainer();
        $container->executeMethod = "";
        $instance = new \WebStream\Test\TestData\AutowiredTest6($container);
        $reader = new AnnotationReader($instance, $container);
        $reader->read();

        $this->assertNull($instance->getInstance());
    }
}
