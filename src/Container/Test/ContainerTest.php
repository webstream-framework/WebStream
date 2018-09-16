<?php
namespace WebStream\Container\Test;

require_once dirname(__FILE__) . '/../Container.php';
require_once dirname(__FILE__) . '/../ValueProxy.php';
require_once dirname(__FILE__) . '/../Test/Providers/ContainerProvider.php';
require_once dirname(__FILE__) . '/../Test/Modules/InvalidArgumentException.php';

use WebStream\Container\Container;
use WebStream\Container\ValueProxy;
use WebStream\Container\Test\Providers\ContainerProvider;

/**
 * ContainerTest
 * @author Ryuichi TANAKA.
 * @since 2016/08/20
 * @version 0.7
 */
class ContainerTest extends \PHPUnit\Framework\TestCase
{
    use ContainerProvider;

    /**
     * 正常系
     * 値を格納して取り出せること
     * @test
     * @dataProvider valueLazyProvider
     */
    public function okContainer($value, $result)
    {
        $container = new Container();
        $container->test = $value;
        $this->assertEquals($container->test, $result);
    }

    /**
     * 正常系
     * 設定した値のカウントが取得できること
     * @test
     */
    public function okLength()
    {
        $container = new Container();
        $container->test1 = 1;
        $container->test2 = 2;
        $this->assertEquals($container->length(), 2);
    }

    /**
     * 正常系
     * 値を削除できること
     * 削除後の要素にアクセスすると例外が発生すること
     * @test
     * @expectedException WebStream\Exception\Extend\InvalidArgumentException
     */
    public function okRemove()
    {
        $container = new Container();
        $container->test1 = 1;
        $container->remove("test1");
        $this->assertNull($container->test1);
    }

    /**
     * 正常系
     * プリミティブ値を登録できること
     * 値はそのまま保存される
     * @test
     * @dataProvider valueDynamicProvider
     */
    public function okRegisterPrimitive($value, $result)
    {
        $container = new Container();
        $container->register("test", $value);
        $this->assertEquals($container->test, $result);
    }

    /**
     * 正常系
     * クロージャを登録できること
     * 値はそのまま保存される
     * @test
     */
    public function okRegisterClosure()
    {
        $func = function() {
            return "test";
        };
        $container = new Container();
        $container->register("test", $func);
        $this->assertInternalType("object", $container->test);
        $result = $container->test;
        $this->assertEquals($result(), "test");
    }

    /**
     * 正常系
     * 即時実行状態で登録されること
     * @test
     */
    public function okRegisterAsDynamic()
    {
        $func = function() {
            echo "evaluated";
            return "test";
        };
        $container = new Container();
        $container->registerAsDynamic("test", $func);
        $this->expectOutputString("evaluated");
        $this->assertEquals($container->test, "test");
    }

    /**
     * 正常系
     * 遅延実行状態で登録されること
     * @test
     */
    public function okRegisterAsLazy()
    {
        $func = function() {
            echo "evaluated";
            return "test";
        };
        $container = new Container();
        $container->registerAsLazy("test", $func);
        $this->expectOutputString(null);
        $this->assertEquals($container->test, "test");
        $this->expectOutputString("evaluated");
    }

    /**
     * 正常系
     * コンテナから取得するとき、値はキャッシュされていること
     * @test
     */
    public function okRegisterCached()
    {
        $func = function() {
            echo "evaluated";
            return "test";
        };

        $container = new Container();
        // $container->registerAsLazyUnCached("test", $func);

        ob_start();
        $container->registerAsDynamic("test", $func);
        $actual = ob_get_clean();
        $this->assertEquals($actual, "evaluated");
        $this->assertEquals($container->test, "test");

        ob_start();
        $container->registerAsLazy("test", $func);
        $this->assertEquals($container->test, "test");
        $actual = ob_get_clean();
        $this->assertEquals($actual, "evaluated"); // uncached
    }

    /**
     * 正常系
     * コンテナから取得するとき、値はキャッシュされていないこと
     * @test
     */
    public function okRegisterUnCached()
    {
        $func = function() {
            echo "evaluated";
            return "test";
        };

        $container = new Container();
        $container->registerAsLazyUnCached("test", $func);

        ob_start();
        $this->assertEquals($container->test, "test");
        $actual = ob_get_clean();
        $this->assertEquals($actual, "evaluated");

        ob_start();
        $this->assertEquals($container->test, "test");
        $actual = ob_get_clean();
        $this->assertEquals($actual, "evaluated"); // uncached
    }

    /**
     * 正常系
     * 未定義の値にアクセスしても例外が発生しないこと
     * @test
     */
    public function okUnStrictContainer()
    {
        $container = new Container(false);
        $this->assertNull($container->undefined);
    }
}
