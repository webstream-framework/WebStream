<?php
namespace WebStream\Test;

require_once '../Module/ClassLoader.php';

/**
 * ユニットテスト基底クラス
 * @author Ryuichi TANAKA.
 * @since 2013/09/02
 * @version 0.4
 */
class TestBase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $classLoader = new \WebStream\Module\ClassLoader();
        spl_autoload_register(array($classLoader, "load"));
    }

    public function tearDown()
    {
    }
}
