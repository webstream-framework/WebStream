<?php
namespace WebStream\Exception\Test\Providers;

use WebStream\Exception\ApplicationException;
use WebStream\Exception\SystemException;
use WebStream\Exception\Extend\AnnotationException;
use WebStream\Exception\Extend\ClassNotFoundException;
use WebStream\Exception\Extend\CollectionException;
use WebStream\Exception\Extend\CsrfException;
use WebStream\Exception\Extend\DatabaseException;
use WebStream\Exception\Extend\ForbiddenAccessException;
use WebStream\Exception\Extend\InvalidArgumentException;
use WebStream\Exception\Extend\InvalidRequestException;
use WebStream\Exception\Extend\IOException;
use WebStream\Exception\Extend\LoggerException;
use WebStream\Exception\Extend\MethodNotFoundException;
use WebStream\Exception\Extend\ResourceNotFoundException;
use WebStream\Exception\Extend\RouterException;
use WebStream\Exception\Extend\SessionTimeoutException;
use WebStream\Exception\Extend\ValidateException;

/**
 * ExceptionProvider
 * @author Ryuichi TANAKA.
 * @since 2017/01/08
 * @version 0.7
 */
trait ExceptionProvider
{
    public function exceptionProvider()
    {
        return [
            [new SystemException("error message")],
            [new ApplicationException("error message")],
            [new SystemException("")],
            [new ApplicationException("")],
            [new AnnotationException("error message")],
            [new ClassNotFoundException("error message")],
            [new CollectionException("error message")],
            [new CsrfException("error message")],
            [new DatabaseException("error message")],
            [new ForbiddenAccessException("error message")],
            [new InvalidArgumentException("error message")],
            [new InvalidRequestException("error message")],
            [new IOException("error message")],
            [new LoggerException("error message")],
            [new MethodNotFoundException("error message")],
            [new ResourceNotFoundException("error message")],
            [new RouterException("error message")],
            [new SessionTimeoutException("error message")],
            [new ValidateException("error message")]
        ];
    }
}
