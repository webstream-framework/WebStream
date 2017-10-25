<?php
namespace WebStream\Annotation\Test;

require_once dirname(__FILE__) . '/../Modules/DI/Injector.php';
require_once dirname(__FILE__) . '/../Modules/Container/Container.php';
require_once dirname(__FILE__) . '/../Modules/IO/File.php';
require_once dirname(__FILE__) . '/../Modules/Exception/ApplicationException.php';
require_once dirname(__FILE__) . '/../Modules/Exception/Extend/DatabaseException.php';
require_once dirname(__FILE__) . '/../Modules/Exception/Extend/IOException.php';
require_once dirname(__FILE__) . '/../Modules/Exception/Delegate/ExceptionDelegator.php';
require_once dirname(__FILE__) . '/../Base/Annotation.php';
require_once dirname(__FILE__) . '/../Base/IAnnotatable.php';
require_once dirname(__FILE__) . '/../Base/IClass.php';
require_once dirname(__FILE__) . '/../Base/IRead.php';
require_once dirname(__FILE__) . '/../Reader/AnnotationReader.php';
require_once dirname(__FILE__) . '/../Attributes/Database.php';
require_once dirname(__FILE__) . '/../Test/Providers/DatabaseAnnotationProvider.php';
require_once dirname(__FILE__) . '/../Test/Fixtures/DatabaseFixture1.php';
require_once dirname(__FILE__) . '/../Test/Fixtures/DatabaseFixture2.php';
require_once dirname(__FILE__) . '/../Test/Fixtures/DatabaseDriverFixture.php';

use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Annotation\Attributes\Database;
use WebStream\Annotation\Test\Providers\DatabaseAnnotationProvider;
use WebStream\Container\Container;
use WebStream\Exception\Extend\AnnotationException;

/**
 * DatabaseAnnotationTest
 * @author Ryuichi TANAKA.
 * @since 2017/01/14
 * @version 0.7
 */
class DatabaseAnnotationTest extends \PHPUnit\Framework\TestCase
{
    use DatabaseAnnotationProvider;

    /**
     * 正常系
     * テンプレート情報を読み込めること
     * @test
     * @dataProvider okProvider
     */
    public function okAnnotationTest($clazz, $action, $rootPath, $result)
    {
        $instance = new $clazz();
        $container = new Container();
        $container->rootPath = $rootPath;
        $annotaionReader = new AnnotationReader($instance);
        $annotaionReader->setActionMethod($action);
        $annotaionReader->readable(Database::class, $container);
        $annotaionReader->readClass();
        $annotation = $annotaionReader->getAnnotationInfoList();

        $this->assertArraySubset(
            [Database::class => $result],
            $annotaionReader->getAnnotationInfoList()
        );
    }

    /**
     * 異常系
     * データベースドライバが読み込めない場合、例外が発生すること
     * @test
     * @dataProvider ngProvider
     * @expectedException WebStream\Exception\Extend\DatabaseException
     */
    public function ngAnnotationTest($clazz, $action)
    {
        $instance = new $clazz();
        $container = new Container();
        $annotaionReader = new AnnotationReader($instance);
        $annotaionReader->setActionMethod($action);
        $annotaionReader->readable(Database::class, $container);
        $annotaionReader->readClass();
        $annotation = $annotaionReader->getAnnotationInfoList();
        $exception = $annotaionReader->getException();

        $this->assertNotNull($exception);
        $exception->raise();
    }
}
