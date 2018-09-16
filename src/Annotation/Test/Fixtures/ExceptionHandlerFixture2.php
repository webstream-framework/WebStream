<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\ExceptionHandler;
use WebStream\Exception\ApplicationException;
use WebStream\Exception\Extend\InvalidArgumentException;

/**
 * 捕捉例外のサブクラスの例外が発生した場合
 */
class ExceptionHandlerFixture2 implements IAnnotatable
{
    public function action()
    {
        throw new InvalidArgumentException("message");
    }

    /**
     * @ExceptionHandler("WebStream\Exception\ApplicationException")
     */
    public function error()
    {
    }
}
