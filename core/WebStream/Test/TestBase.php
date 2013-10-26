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
        spl_autoload_register([$classLoader, "load"]);
        register_shutdown_function('WebStream\Module\shutdownHandler');
    }

    protected function preloadClass()
    {
        $classLoader = new \WebStream\Module\ClassLoader();
        $classLoader->load([
            "AbstractAnnotation",
            "Autowired",
            "Value",
            "Type",
            "Inject",
            "Filter",
            "Template",
            "Header",
            "Doctrine/Common/Annotations/AnnotationException"
        ]);
    }
}
