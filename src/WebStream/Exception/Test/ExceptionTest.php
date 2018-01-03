<?php
namespace WebStream\Exception\Test;

require_once dirname(__FILE__) . '/../Modules/DI/Injector.php';
require_once dirname(__FILE__) . '/../Test/Providers/ExceptionProvider.php';
require_once dirname(__FILE__) . '/../ApplicationException.php';
require_once dirname(__FILE__) . '/../SystemException.php';
require_once dirname(__FILE__) . '/../Extend/AnnotationException.php';
require_once dirname(__FILE__) . '/../Extend/ClassNotFoundException.php';
require_once dirname(__FILE__) . '/../Extend/CollectionException.php';
require_once dirname(__FILE__) . '/../Extend/CsrfException.php';
require_once dirname(__FILE__) . '/../Extend/DatabaseException.php';
require_once dirname(__FILE__) . '/../Extend/ForbiddenAccessException.php';
require_once dirname(__FILE__) . '/../Extend/InvalidArgumentException.php';
require_once dirname(__FILE__) . '/../Extend/InvalidRequestException.php';
require_once dirname(__FILE__) . '/../Extend/IOException.php';
require_once dirname(__FILE__) . '/../Extend/LoggerException.php';
require_once dirname(__FILE__) . '/../Extend/MethodNotFoundException.php';
require_once dirname(__FILE__) . '/../Extend/OutOfBoundsException.php';
require_once dirname(__FILE__) . '/../Extend/ResourceNotFoundException.php';
require_once dirname(__FILE__) . '/../Extend/RouterException.php';
require_once dirname(__FILE__) . '/../Extend/SessionTimeoutException.php';
require_once dirname(__FILE__) . '/../Extend/ValidateException.php';

use WebStream\Exception\Test\Providers\ExceptionProvider;

/**
* ExceptionTest
* @author Ryuichi TANAKA.
* @since 2017/01/07
* @version 0.7
 */
class ExceptionTest extends \PHPUnit\Framework\TestCase
{
    use ExceptionProvider;

    /**
     * 正常系
     * 例外オブジェクトのメッセージを取得できること
     * @test
     * @dataProvider exceptionProvider
     */
    public function okExceptionMessageTest($exception)
    {
        $this->assertNotNull($exception->getExceptionAsString());
    }
}
