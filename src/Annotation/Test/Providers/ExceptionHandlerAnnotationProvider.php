<?php
namespace WebStream\Annotation\Test\Providers;

use WebStream\Annotation\Test\Fixtures\ExceptionHandlerFixture1;
use WebStream\Annotation\Test\Fixtures\ExceptionHandlerFixture2;
use WebStream\Annotation\Test\Fixtures\ExceptionHandlerFixture3;
use WebStream\Annotation\Test\Fixtures\ExceptionHandlerFixture4;
use WebStream\Exception\ApplicationException;
use WebStream\Exception\Extend\IOException;
use WebStream\Exception\Extend\ValidateException;

/**
 * ExceptionHandlerAnnotationProvider
 * @author Ryuichi TANAKA.
 * @since 2017/01/09
 * @version 0.7
 */
trait ExceptionHandlerAnnotationProvider
{
    public function okProvider()
    {
        return [
            [ExceptionHandlerFixture1::class, ApplicationException::class, "action"],
            [ExceptionHandlerFixture2::class, ApplicationException::class, "action"],
            [ExceptionHandlerFixture3::class, ValidateException::class, "action1"],
            [ExceptionHandlerFixture3::class, IOException::class, "action2"],
            [ExceptionHandlerFixture4::class, ValidateException::class, "action1"],
            [ExceptionHandlerFixture4::class, IOException::class, "action2"]
        ];
    }
}
