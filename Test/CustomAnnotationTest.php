<?php
namespace WebStream\Annotation\Test;

require_once dirname(__FILE__) . '/../Modules/Container/Container.php';
require_once dirname(__FILE__) . '/../Modules/Exception/ApplicationException.php';
require_once dirname(__FILE__) . '/../Modules/Exception/Delegate/ExceptionDelegator.php';
require_once dirname(__FILE__) . '/../Base/Annotation.php';
require_once dirname(__FILE__) . '/../Base/IAnnotatable.php';
require_once dirname(__FILE__) . '/../Base/IMethod.php';
require_once dirname(__FILE__) . '/../Base/IRead.php';
require_once dirname(__FILE__) . '/../Reader/AnnotationReader.php';
require_once dirname(__FILE__) . '/../Test/Fixtures/CustomAnnotation.php';
require_once dirname(__FILE__) . '/../Test/Fixtures/CustomAnnotationFixture1.php';

use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Annotation\Test\Fixtures\CustomAnnotation;
use WebStream\Annotation\Test\Fixtures\CustomAnnotationFixture1;

/**
 * CustomAnnotationTest
 * @author Ryuichi TANAKA.
 * @since 2018/02/15
 * @version 0.7
 */
class CustomAnnotationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * 正常系
     * カスタムアノテーション定義が実行されること
     * @test
     */
    public function okAnnotationTest()
    {
        $instance = new CustomAnnotationFixture1();
        $annotaionReader = new AnnotationReader($instance);
        $annotaionReader->readMethod();
        $this->expectOutputString("test");
    }
}
