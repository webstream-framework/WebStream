<?php
namespace WebStream\Database\Test\Providers;

/**
 * DatabaseProvider
 * @author Ryuichi TANAKA.
 * @since 2017/11/12
 * @version 0.7
 */
trait DatabaseProvider
{
    public function selectProvider()
    {
        return [
            ['SELECT * FROM T_WebStream', [], [['id' => 1, 'name' => 'test1'], ['id' => 2, 'name' => 'test2']], 'WebStream\Database\Driver\Mysql', '/Fixtures/database.mysql.yml'],
            ['SELECT * FROM T_WebStream WHERE id = :id', ['id' => 1], [['id' => 1, 'name' => 'test1']], 'WebStream\Database\Driver\Mysql', '/Fixtures/database.mysql.yml'],
            ['SELECT * FROM T_WebStream', [], [['id' => 1, 'name' => 'test1'], ['id' => 2, 'name' => 'test2']], 'WebStream\Database\Driver\Postgresql', '/Fixtures/database.postgres.yml'],
            ['SELECT * FROM T_WebStream WHERE id = :id', ['id' => 1], [['id' => 1, 'name' => 'test1']], 'WebStream\Database\Driver\Postgresql', '/Fixtures/database.postgres.yml'],
            ['SELECT * FROM T_WebStream', [], [['id' => 1, 'name' => 'test1'], ['id' => 2, 'name' => 'test2']], 'WebStream\Database\Driver\Sqlite', '/Fixtures/database.sqlite.yml'],
            ['SELECT * FROM T_WebStream WHERE id = :id', ['id' => 1], [['id' => 1, 'name' => 'test1']], 'WebStream\Database\Driver\Sqlite', '/Fixtures/database.sqlite.yml']
        ];
    }

    public function insertProvider()
    {
        return [
            ['INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test3'], 1, 'WebStream\Database\Driver\Mysql', '/Fixtures/database.mysql.yml'],
            ['INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test3'], 1, 'WebStream\Database\Driver\Postgresql', '/Fixtures/database.postgres.yml'],
            ['INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test3'], 1, 'WebStream\Database\Driver\Sqlite', '/Fixtures/database.sqlite.yml']
        ];
    }

    public function updateProvider()
    {
        return [
            ['UPDATE T_WebStream SET name = :name WHERE id = :id', ['id' => 1, 'name' => 'test4'], 1, 'WebStream\Database\Driver\Mysql', '/Fixtures/database.mysql.yml'],
            ['UPDATE T_WebStream SET name = :name WHERE id = :id', ['id' => 1, 'name' => 'test4'], 1, 'WebStream\Database\Driver\Postgresql', '/Fixtures/database.postgres.yml'],
            ['UPDATE T_WebStream SET name = :name WHERE id = :id', ['id' => 1, 'name' => 'test4'], 1, 'WebStream\Database\Driver\Sqlite', '/Fixtures/database.sqlite.yml']
        ];
    }

    public function deleteProvider()
    {
        return [
            ['DELETE FROM T_WebStream', 'WebStream\Database\Driver\Mysql', '/Fixtures/database.mysql.yml'],
            ['DELETE FROM T_WebStream', 'WebStream\Database\Driver\Postgresql', '/Fixtures/database.postgres.yml'],
            ['DELETE FROM T_WebStream', 'WebStream\Database\Driver\Sqlite', '/Fixtures/database.sqlite.yml']
        ];
    }

    public function transactionProvider()
    {
        return [
            ['WebStream\Database\Driver\Mysql', '/Fixtures/database.mysql.yml'],
            ['WebStream\Database\Driver\Postgresql', '/Fixtures/database.postgres.yml'],
            ['WebStream\Database\Driver\Sqlite', '/Fixtures/database.sqlite.yml']
        ];
    }
}
