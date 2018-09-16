<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\ExceptionHandler;
use WebStream\Exception\Extend\IOException;
use WebStream\Exception\Extend\ValidateException;

/**
 * 捕捉例外を複数指定した場合
 */
class ExceptionHandlerFixture4 implements IAnnotatable
{
    public function action1()
    {
        throw new ValidateException("message");
    }

    public function action2()
    {
        throw new IOException("message");
    }

    /**
     * @ExceptionHandler({"WebStream\Exception\Extend\ValidateException", "WebStream\Exception\Extend\IOException"})
     */
    public function error()
    {
    }
}
