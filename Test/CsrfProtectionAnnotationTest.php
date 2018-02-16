<?php
namespace WebStream\Annotation\Test;

require_once dirname(__FILE__) . '/../Modules/Container/Container.php';
require_once dirname(__FILE__) . '/../Modules/Exception/ApplicationException.php';
require_once dirname(__FILE__) . '/../Modules/Exception/Extend/CsrfException.php';
require_once dirname(__FILE__) . '/../Modules/Exception/Delegate/ExceptionDelegator.php';
require_once dirname(__FILE__) . '/../Base/Annotation.php';
require_once dirname(__FILE__) . '/../Base/IAnnotatable.php';
require_once dirname(__FILE__) . '/../Base/IMethod.php';
require_once dirname(__FILE__) . '/../Reader/AnnotationReader.php';
require_once dirname(__FILE__) . '/../Attributes/CsrfProtection.php';
require_once dirname(__FILE__) . '/../Test/Providers/CsrfProtectionAnnotationProvider.php';
require_once dirname(__FILE__) . '/../Test/Fixtures/CsrfProtectionFixture1.php';

use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Annotation\Attributes\CsrfProtection;
use WebStream\Annotation\Test\Providers\CsrfProtectionAnnotationProvider;
use WebStream\Annotation\Test\Fixtures\DummySession;
use WebStream\Container\Container;

/**
 * CsrfProtectionAnnotationTest
 * @author Ryuichi TANAKA.
 * @since 2017/01/11
 * @version 0.7
 */
class CsrfProtectionAnnotationTest extends \PHPUnit\Framework\TestCase
{
    use CsrfProtectionAnnotationProvider;

    /**
     * 正常系
     * CSRFエラーが起きないこと
     * @test
     * @dataProvider okProvider
     */
    public function okAnnotationTest($clazz, $requestMethod, $post, $header)
    {
        $instance = new $clazz();
        $container = new Container();
        $container->requestMethod = $requestMethod;
        $container->post = $post;
        $container->header = $header;
        $session = new DummySession("abcde");
        $container->session = $session;
        $annotaionReader = new AnnotationReader($instance);
        $annotaionReader->setActionMethod("action");
        $annotaionReader->readable(CsrfProtection::class, $container);
        $annotaionReader->readMethod();
        $exception = $annotaionReader->getException();

        $this->assertNull($exception);
    }

    /**
     * 異常系
     * CSRFエラーが起きること
     * @test
     * @dataProvider ngProvider
     * @expectedException WebStream\Exception\Extend\CsrfException
     */
    public function ngAnnotationTest($clazz, $requestMethod, $post, $header)
    {
        $instance = new $clazz();
        $container = new Container();
        $container->requestMethod = $requestMethod;
        $container->post = $post;
        $container->header = $header;
        $session = new DummySession("abcde");
        $container->session = $session;
        $annotaionReader = new AnnotationReader($instance);
        $annotaionReader->setActionMethod("action");
        $annotaionReader->readable(CsrfProtection::class, $container);
        $annotaionReader->readMethod();
        $exception = $annotaionReader->getException();

        $this->assertNotNull($exception);
        $exception->raise();
    }
}
