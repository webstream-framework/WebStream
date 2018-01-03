<?php
namespace WebStream\Annotation\Test;

require_once dirname(__FILE__) . '/../Modules/DI/Injector.php';
require_once dirname(__FILE__) . '/../Modules/Container/Container.php';
require_once dirname(__FILE__) . '/../Modules/Container/ValueProxy.php';
require_once dirname(__FILE__) . '/../Modules/Exception/ApplicationException.php';
require_once dirname(__FILE__) . '/../Modules/Exception/Extend/AnnotationException.php';
require_once dirname(__FILE__) . '/../Modules/Exception/Extend/DatabaseException.php';
require_once dirname(__FILE__) . '/../Modules/Exception/Delegate/ExceptionDelegator.php';
require_once dirname(__FILE__) . '/../Modules/IO/InputStream.php';
require_once dirname(__FILE__) . '/../Modules/IO/File.php';
require_once dirname(__FILE__) . '/../Modules/IO/FileInputStream.php';
require_once dirname(__FILE__) . '/../Modules/IO/Reader/InputStreamReader.php';
require_once dirname(__FILE__) . '/../Base/Annotation.php';
require_once dirname(__FILE__) . '/../Base/IAnnotatable.php';
require_once dirname(__FILE__) . '/../Base/IMethods.php';
require_once dirname(__FILE__) . '/../Base/IRead.php';
require_once dirname(__FILE__) . '/../Reader/AnnotationReader.php';
require_once dirname(__FILE__) . '/../Reader/Extend/ExtendReader.php';
require_once dirname(__FILE__) . '/../Reader/Extend/QueryExtendReader.php';
require_once dirname(__FILE__) . '/../Container/AnnotationContainer.php';
require_once dirname(__FILE__) . '/../Container/AnnotationListContainer.php';
require_once dirname(__FILE__) . '/../Attributes/Query.php';
require_once dirname(__FILE__) . '/../Test/Providers/QueryAnnotationProvider.php';
require_once dirname(__FILE__) . '/../Test/Fixtures/QueryFixture1.php';
require_once dirname(__FILE__) . '/../Test/Fixtures/QueryFixture2.php';
require_once dirname(__FILE__) . '/../Test/Fixtures/QueryFixture3.php';

use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Annotation\Reader\Extend\QueryExtendReader;
use WebStream\Annotation\Attributes\Query;
use WebStream\Annotation\Test\Providers\QueryAnnotationProvider;
use WebStream\Container\Container;
use WebStream\Exception\Extend\AnnotationException;

/**
 * QueryAnnotationTest
 * @author Ryuichi TANAKA.
 * @since 2017/01/15
 * @version 0.7
 */
class QueryAnnotationTest extends \PHPUnit\Framework\TestCase
{
    use QueryAnnotationProvider;

    /**
     * 正常系
     * クエリ情報を読み込めること
     * @test
     * @dataProvider okProvider
     */
    public function okAnnotationTest($clazz, $action, $rootPath, $key, $result)
    {
        $instance = new $clazz();
        $container = new Container();
        $container->rootPath = $rootPath;
        $annotaionReader = new AnnotationReader($instance);
        $annotaionReader->setActionMethod($action);
        $annotaionReader->readable(Query::class, $container);
        $annotaionReader->useExtendReader(Query::class, QueryExtendReader::class);
        $annotaionReader->readMethod();
        $annotation = $annotaionReader->getAnnotationInfoList();
        $namespace = "WebStream\Annotation\Test";
        $method = $key;
        $queryKey = "WebStream\Annotation\Test\Fixtures\QueryFixture1#action1";
        $xpath = "//mapper[@namespace='$namespace']/*[@id='$method']";

        $this->assertEquals($annotation[Query::class]($queryKey, $xpath), $result);
    }

    /**
     * 異常系
     * クエリファイルパスが間違っている場合、例外が発生すること
     * @test
     * @dataProvider ng1Provider
     * @expectedException WebStream\Exception\Extend\DatabaseException
     */
    public function ngAnnotationInvalidFileFormatTest($clazz, $action, $rootPath)
    {
        $instance = new $clazz();
        $container = new Container();
        $container->rootPath = $rootPath;
        $annotaionReader = new AnnotationReader($instance);
        $annotaionReader->setActionMethod($action);
        $annotaionReader->readable(Query::class, $container);
        $annotaionReader->useExtendReader(Query::class, QueryExtendReader::class);
        $annotaionReader->readMethod();
        $exception = $annotaionReader->getException();

        $this->assertNotNull($exception);
        $exception->raise();
    }

    /**
     * 異常系
     * クエリファイルパスが間違っている場合、例外が発生すること
     * @test
     * @dataProvider ng2Provider
     * @expectedException WebStream\Exception\Extend\DatabaseException
     */
    public function ngAnnotationInvalidFilePathTest($clazz, $action, $rootPath)
    {
        $instance = new $clazz();
        $container = new Container();
        $container->rootPath = $rootPath;
        $annotaionReader = new AnnotationReader($instance);
        $annotaionReader->setActionMethod($action);
        $annotaionReader->readable(Query::class, $container);
        $annotaionReader->useExtendReader(Query::class, QueryExtendReader::class);
        $annotaionReader->readMethod();
        $exception = $annotaionReader->getException();

        $this->assertNotNull($exception);
        $exception->raise();
    }
}
