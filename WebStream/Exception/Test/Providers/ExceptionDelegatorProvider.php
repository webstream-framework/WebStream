<?php
namespace WebStream\Exception\Test\Providers;

use WebStream\Exception\ApplicationException;
use WebStream\Exception\SystemException;
use WebStream\Exception\DelegateException;

/**
 * ExceptionDelegatorProvider
 * @author Ryuichi TANAKA.
 * @since 2017/01/08
 * @version 0.7
 */
trait ExceptionDelegatorProvider
{
    public function exceptionProvider()
    {
        return [
            [new SystemException("error message"), new SystemException("")],
            [new ApplicationException("error message"), new ApplicationException("")],
            [SystemException::class, new SystemException("")],
            [ApplicationException::class, new ApplicationException("")],
        ];
    }
}
