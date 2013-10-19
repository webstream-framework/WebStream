<?php
namespace WebStream\Exception;

/**
 * ClassNotFoundException
 * @author Ryuichi TANAKA.
 * @since 2013/09/22
 * @version 0.4
 */
class ClassNotFoundException extends \Exception
{
    public function __call($name, $arguments) {
        throw $this;
    }
}
