<?php
namespace WebStream\ClassLoader\Test\Fixtures;

class DummyLogger
{
    public function debug($message)
    {
        echo $message;
    }
}
