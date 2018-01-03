<?php
namespace WebStream\Annotation\Test;

require_once dirname(__FILE__) . '/../Modules/DI/Injector.php';
require_once dirname(__FILE__) . '/../Modules/Container/Container.php';
require_once dirname(__FILE__) . '/../Modules/Exception/ApplicationException.php';
require_once dirname(__FILE__) . '/../Modules/Exception/Extend/AnnotationException.php';
require_once dirname(__FILE__) . '/../Modules/Exception/Delegate/ExceptionDelegator.php';
require_once dirname(__FILE__) . '/../Base/Annotation.php';
require_once dirname(__FILE__) . '/../Base/IAnnotatable.php';
require_once dirname(__FILE__) . '/../Base/IMethod.php';
require_once dirname(__FILE__) . '/../Base/IRead.php';
require_once dirname(__FILE__) . '/../Reader/AnnotationReader.php';
require_once dirname(__FILE__) . '/../Attributes/Template.php';
require_once dirname(__FILE__) . '/../Test/Providers/TemplateAnnotationProvider.php';
require_once dirname(__FILE__) . '/../Test/Fixtures/TemplateFixture1.php';

use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Annotation\Attributes\Template;
use WebStream\Annotation\Test\Providers\TemplateAnnotationProvider;
use WebStream\Container\Container;
use WebStream\Exception\Extend\AnnotationException;

/**
 * TemplateAnnotationTest
 * @author Ryuichi TANAKA.
 * @since 2017/01/14
 * @version 0.7
 */
class TemplateAnnotationTest extends \PHPUnit\Framework\TestCase
{
    use TemplateAnnotationProvider;

    /**
     * 正常系
     * データベース情報を読み込めること
     * @test
     * @dataProvider okProvider
     */
    public function okAnnotationTest($clazz, $action, $result)
    {
        $instance = new $clazz();
        $container = new Container();
        $container->action = $action;
        $container->engine = [
            'basic' => "WebStream\Template\Basic",
            'twig' => "WebStream\Template\Twig"
        ];
        $container->logger = new class() { function __call($name, $args) {} };
        $annotaionReader = new AnnotationReader($instance);
        $annotaionReader->setActionMethod($action);
        $annotaionReader->readable(Template::class, $container);
        $annotaionReader->readMethod();

        $this->assertArraySubset(
            [Template::class => $result],
            $annotaionReader->getAnnotationInfoList()
        );
    }

    /**
     * 異常系
     * テンプレート情報に誤りがある場合、例外が発生すること
     * @test
     * @dataProvider ngProvider
     * @expectedException WebStream\Exception\Extend\AnnotationException
     */
    public function ngAnnotationTest($clazz, $action)
    {
        $instance = new $clazz();
        $container = new Container();
        $container->action = $action;
        $container->engine = [
            'basic' => "WebStream\Template\Basic",
            'twig' => "WebStream\Template\Twig"
        ];
        $container->logger = new class() { function __call($name, $args) {} };
        $annotaionReader = new AnnotationReader($instance);
        $annotaionReader->setActionMethod($action);
        $annotaionReader->readable(Template::class, $container);
        $annotaionReader->readMethod();
        $exception = $annotaionReader->getException();

        $this->assertNotNull($exception);
        $exception->raise();
    }
}
