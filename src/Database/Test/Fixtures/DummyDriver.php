<?php
namespace WebStream\Database\Test\Fixtures;

use WebStream\Database\Driver\DatabaseDriver;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

class DummyDriver extends DatabaseDriver
{
    public function __construct($container)
    {
    }

    public function connect()
    {
        $this->connection = DriverManager::getConnection([
            'path' => 'dummy',
            'driver' => 'pdo_sqlite'
        ]);
    }
}
