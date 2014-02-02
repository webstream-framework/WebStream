<?php
namespace WebStream\Test;

require_once dirname(__FILE__) . '/../Module/Functions.php';
require_once dirname(__FILE__) . '/../Module/ClassLoader.php';

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
        $this->autoLoad();
        $this->preloadClass();
    }

    public function tearDown()
    {
    }

    protected function autoLoad()
    {
        $classLoader = new \WebStream\Module\ClassLoader();
        $classLoader->test();
        spl_autoload_register([$classLoader, "load"]);
        register_shutdown_function('WebStream\Module\shutdownHandler');
    }

    protected function preloadClass()
    {
        $classLoader = new \WebStream\Module\ClassLoader();
        $classLoader->test();
        $classLoader->load([
            "WebStream\Annotation\AbstractAnnotation",
            "WebStream\Annotation\Autowired",
            "WebStream\Annotation\Value",
            "WebStream\Annotation\Type",
            "WebStream\Annotation\Inject",
            "WebStream\Annotation\Filter",
            "WebStream\Annotation\Template",
            "WebStream\Annotation\TemplateCache",
            "WebStream\Annotation\Header",
            "Doctrine/Common/Annotations/AnnotationException"
        ]);
    }
}
