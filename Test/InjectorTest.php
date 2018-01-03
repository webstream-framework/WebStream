<?php
namespace WebStream\DI\Test;

require_once dirname(__FILE__) . '/../Modules/Container/Container.php';
require_once dirname(__FILE__) . '/../Modules/AnnotationException.php';
require_once dirname(__FILE__) . '/Fixtures/Injected.php';
require_once dirname(__FILE__) . '/Fixtures/StrictInjected.php';

/**
 * InjectorTest
 * @author Ryuichi TANAKA.
 * @since 2016/09/11
 * @version 0.7
 */
class InjectorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * 正常系
     * 注入できること
     * @test
     */
    public function okInject()
    {
        $object = new Injected();
        $object->inject('key', 'value');
        $this->assertEquals($object->getValue("key"), "value");
    }

    /**
     * 正常系
     * 型指定による注入ができること
     * @test
     */
    public function okStrictInject()
    {
        $object = new StrictInjected();
        $injectValue = new Sample1();
        $object->strictInject('value', $injectValue);
        $this->assertInstanceOf("WebStream\DI\Test\Sample1", $object->getValue());
    }

    /**
     * 正常系
     * 親クラスの型指定による注入ができること
     * @test
     */
    public function okParentClassStrictInject()
    {
        $object = new StrictInjected();
        $injectValue = new Sample3();
        $object->strictInject('value', $injectValue);
        $this->assertInstanceOf("WebStream\DI\Test\Sample3", $object->getValue());
        $this->assertInstanceOf("WebStream\DI\Test\Sample1", $object->getValue());
    }

    /**
     * 異常系
     * プロパティが定義していない場合、注入されず既定値が返ること
     * @test
     */
    public function ngUndefinedPropertyStrictInject()
    {
        $object = new StrictInjected();
        $injectValue = new Sample1();
        $object->strictInject('undefined', $injectValue);
        $this->assertNull($object->getValue());
    }

    /**
     * 異常系
     * 定義された型と注入する型が不一致の場合、例外が発生すること
     * @test
     * @expectedException WebStream\Exception\Extend\AnnotationException
     */
    public function ngMismatchTypeStrictInject()
    {
        $object = new StrictInjected();
        $injectValue = new Sample2();
        $object->strictInject('value', $injectValue);
    }

    /**
     * 異常系
     * NULL値を注入しようとした場合、例外が発生すること
     * @test
     * @expectedException WebStream\Exception\Extend\AnnotationException
     */
    public function ngNullStrictInject()
    {
        $object = new StrictInjected();
        $object->strictInject('value', null);
    }
}
