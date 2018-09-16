<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\ExceptionHandler;
use WebStream\Exception\ApplicationException;

/**
 * 捕捉例外の同じ例外が発生した場合
 */
class ExceptionHandlerFixture1 implements IAnnotatable
{
    public function action()
    {
        throw new ApplicationException("message");
    }

    /**
     * @ExceptionHandler("WebStream\Exception\ApplicationException")
     */
    public function error()
    {
    }
}
