<?php
namespace WebStream\DI\Test;

require_once dirname(__FILE__) . '/Fixtures/Injected.php';

/**
 * InjectorTest
 * @author Ryuichi TANAKA.
 * @since 2016/09/11
 * @version 0.7
 */
class InjectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * 正常系
     * ファイル名を取得できること
     * @test
     */
    public function okFileName()
    {
        $object = new Injected();
        $object->inject('key', 'value');
        $this->assertEquals($object->getValue("key"), "value");
    }
}
