<?php
namespace WebStream\Test;
use WebStream\Container;
use WebStream\ValueProxy;
use WebStream\Utility;
/**
 * Cacheクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2013/01/12
 */
require_once 'UnitTestBase.php';

class ContainerTest extends UnitTestBase {

    public function setUp() {
        parent::setUp();
        require_once Utility::getRoot() . "/core/test/testdata/classes/Sample.php";
    }

    /**
     * 正常系
     * プリミティブ値をプロパティとしてセットして取得できること
     * @dataProvider setPrimitive
     */
    public function testOkSetPrimitiveAsProperty($key, $value) {
        $container = new Container();
        $container->{$key} = $value;
        $this->assertEquals($container->{$key}, $value);
    }

    /**
     * 正常系
     * プリミティブ値をregisterメソッドでセットして取得できること
     * @dataProvider setPrimitive
     */
    public function testOkSetPrimitiveAsRegister($key, $value) {
        $container = new Container();
        $container->register($key, $value);
        $this->assertEquals($container->{$key}, $value);
    }

    /**
     * 正常系
     * プリミティブ値をメソッドでセットして取得できること
     * @dataProvider setPrimitive
     */
    public function testOkSetPrimitiveAsMethod($key, $value) {
        $container = new Container();
        $container->{$key}($value);
        $this->assertEquals($container->{$key}, $value);
    }

    /**
     * 正常系
     * クロージャをregisterAsLazyメソッドでセットして取得できること
     * @dataProvider setClosure
     */
    public function testOkSetClosureAsRegister($arg1, $arg2) {
        $container = new Container();
        $key = "yuruyuri";
        $container->registerAsLazy($key, function($arg1, $arg2) {
            return new Sample($arg1, $arg2);
        }, $arg1, $arg2);
        $this->assertEquals($container->{$key}->getArg1(), $arg1);
        $this->assertEquals($container->{$key}->getArg2(), $arg2);
    }

    /**
     * 正常系
     * registerAsLazyで登録したとき、実行タイミングが遅延されること
     */
    public function testOkRegisterAsLazy() {
        ob_start();
        $container = new Container();
        $key = "yuruyuri";
        $container->registerAsLazy($key, function() {
            echo "lazy ";
            return "executed";
        });
        $result = ob_get_clean();
        ob_start();
        echo $container->{$key};
        $result2 = ob_get_clean();
        $this->assertEquals($result, "");
        $this->assertEquals($result2, "lazy executed");
    }

    /**
     * 正常系
     * registerAsDynamicで登録したとき、即時実行されること
     */
    public function testOkRegisterAsDynamic() {
        ob_start();
        $container = new Container();
        $key = "yuruyuri";
        $container->registerAsDynamic($key, function() {
            echo "dynamic";
            return "executed";
        });
        $result = ob_get_clean();
        ob_start();
        echo $container->{$key};
        $result2 = ob_get_clean();
        $this->assertEquals($result, "dynamic");
        $this->assertEquals($result2, "executed");
    }

    /**
     * 正常系
     * registerAsLazyのエイリアス構文で登録できること
     * registerAsLazyの第一引数をメソッドとして実行
     * @dataProvider setClosure
     */
    public function testOkRegisterAsLazyAlias1($arg1, $arg2) {
        $container = new Container();
        $container->yuruyuri(function($arg1, $arg2) {
            return new Sample($arg1, $arg2);
        }, $arg1, $arg2);
        $this->assertEquals($container->yuruyuri->getArg1(), $arg1);
        $this->assertEquals($container->yuruyuri->getArg2(), $arg2);
    }

    /**
     * 正常系
     * registerAsLazyのエイリアス構文で登録できること
     * registerAsLazyの第一引数をメソッドとして実行
     * 引数をuse句で渡せること
     * @dataProvider setClosure
     */
    public function testOkRegisterAsLazyAlias2($arg1, $arg2) {
        $container = new Container();
        $container->yuruyuri(function($arg1, $arg2) use ($arg1, $arg2) {
            return new Sample($arg1, $arg2);
        });
        $this->assertEquals($container->yuruyuri->getArg1(), $arg1);
        $this->assertEquals($container->yuruyuri->getArg2(), $arg2);
    }

    /**
     * 正常系
     * registerAsLazyのエイリアス構文で登録できること
     * registerAsLazyをプロパティとして実行
     * 引数をuse句で渡すことが必須
     * @dataProvider setClosure
     */
    public function testOkRegisterAsLazyAlias3($arg1, $arg2) {
        $container = new Container();
        $container->yuruyuri = function($arg1, $arg2) use ($arg1, $arg2) {
            return new Sample($arg1, $arg2);
        };
        $this->assertEquals($container->yuruyuri->getArg1(), $arg1);
        $this->assertEquals($container->yuruyuri->getArg2(), $arg2);
    }

    /**
     * 異常系
     * registerAsLazyで存在しないキーを取得したとき、例外が発生すること
     * @expectedException InvalidArgumentException
     * @dataProvider setClosure
     */
    public function testNgUndefinedKeyAccess($arg1, $arg2) {
        $container = new Container();
        $container->registerAsLazy("yuruyuri", function($arg1, $arg2) {
            return new Sample($arg1, $arg2);
        }, $arg1, $arg2);
        $container->yuruyuri2->getArg1();
    }
}