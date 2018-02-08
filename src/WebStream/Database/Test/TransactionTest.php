<?php
namespace WebStream\Database\Test;

require_once dirname(__FILE__) . '/../Modules/IO/File.php';
require_once dirname(__FILE__) . '/../Modules/Container/Container.php';
require_once dirname(__FILE__) . '/../Modules/Container/ValueProxy.php';
require_once dirname(__FILE__) . '/../Modules/DI/Injector.php';
require_once dirname(__FILE__) . '/../Modules/Exception/ApplicationException.php';
require_once dirname(__FILE__) . '/../Modules/Exception/Extend/DatabaseException.php';
require_once dirname(__FILE__) . '/../Driver/DatabaseDriver.php';
require_once dirname(__FILE__) . '/../Driver/Mysql.php';
require_once dirname(__FILE__) . '/../Driver/Postgresql.php';
require_once dirname(__FILE__) . '/../Driver/Sqlite.php';
require_once dirname(__FILE__) . '/../ConnectionManager.php';
require_once dirname(__FILE__) . '/../DatabaseManager.php';
require_once dirname(__FILE__) . '/../Query.php';
require_once dirname(__FILE__) . '/../Result.php';
require_once dirname(__FILE__) . '/../ResultEntity.php';
require_once dirname(__FILE__) . '/../EntityManager.php';
require_once dirname(__FILE__) . '/../EntityProperty.php';
require_once dirname(__FILE__) . '/../Test/Fixtures/DummyLogger.php';
require_once dirname(__FILE__) . '/../Test/Providers/DatabaseProvider.php';

use WebStream\Container\Container;
use WebStream\Database\DatabaseManager;
use WebStream\Database\Test\Fixtures\DummyLogger;
use WebStream\Database\Test\Providers\DatabaseProvider;
use WebStream\Exception\Extend\DatabaseException;
use Doctrine\DBAL\Connection;

/**
 * TransactionTest
 * @author Ryuichi TANAKA.
 * @since 2017/12/17
 * @version 0.7
 */
class TransactionTest extends \PHPUnit\Framework\TestCase
{
    use DatabaseProvider;

    public function setUp()
    {
        $driverClassPathList = [
            'WebStream\Database\Driver\Mysql',
            'WebStream\Database\Driver\Postgresql',
            'WebStream\Database\Driver\Sqlite',
        ];

        $configPathList = [
             dirname(__FILE__) . '/Fixtures/database.mysql.yml',
             dirname(__FILE__) . '/Fixtures/database.postgres.yml',
             dirname(__FILE__) . '/Fixtures/database.sqlite.yml'
        ];

        for ($i = 0; $i < 3; $i++) {
            $container = new Container();
            $container->logger = new DummyLogger();
            $config = new Container();
            $config->configPath = $configPathList[$i];
            $config->driverClassPath = $driverClassPathList[$i];
            $config->filepath = "test";
            $container->connectionContainerList = [$config];
            $manager = new DatabaseManager($container);
            $manager->loadConnection($config->filepath);
            $manager->connect();
            $manager->enableAutoCommit();
            $manager->query('DELETE FROM T_WebStream')->delete();
            $manager->disconnect();
        }
    }

    /**
     * 正常系
     * commitが実行できること
     * @test
     * @dataProvider transactionProvider
     */
    public function okCommit($driverClassPath, $configPath)
    {
        $container = new Container();
        $container->logger = new DummyLogger();
        $config = new Container();
        $config->configPath = dirname(__FILE__) . $configPath;
        $config->driverClassPath = $driverClassPath;
        $config->filepath = "test";
        $container->connectionContainerList = [$config];

        $manager = new DatabaseManager($container);
        $manager->loadConnection($config->filepath);

        $manager->connect();
        $manager->beginTransaction(Connection::TRANSACTION_READ_COMMITTED);
        $manager->query('INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test'])->insert();
        $manager->query('INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test'])->insert();
        $manager->query('INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test'])->insert();
        $manager->query('INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test'])->insert();
        $manager->commit();
        $manager->disconnect();

        $manager = new DatabaseManager($container);
        $manager->loadConnection($config->filepath);
        $manager->connect();
        $result = $manager->query('SELECT COUNT(*) AS count FROM T_WebStream')->select();
        $rowCount = (int)$result[0]['count'];
        $manager->disconnect();

        $this->assertEquals(4, $rowCount);
    }

    /**
     * 正常系
     * rollbackが実行できること
     * @test
     * @dataProvider transactionProvider
     */
    public function okRollback($driverClassPath, $configPath)
    {
        $container = new Container();
        $container->logger = new DummyLogger();
        $config = new Container();
        $config->configPath = dirname(__FILE__) . $configPath;
        $config->driverClassPath = $driverClassPath;
        $config->filepath = "test";
        $container->connectionContainerList = [$config];

        $manager = new DatabaseManager($container);
        $manager->loadConnection($config->filepath);

        $manager->connect();
        $manager->beginTransaction(Connection::TRANSACTION_READ_COMMITTED);
        $manager->disableAutoCommit();
        $manager->query('INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test'])->insert();
        $manager->rollback();
        $manager->disconnect();

        $manager = new DatabaseManager($container);
        $manager->loadConnection($config->filepath);
        $manager->connect();
        $result = $manager->query('SELECT COUNT(*) AS count FROM T_WebStream')->select();
        $rowCount = (int)$result[0]['count'];

        $this->assertEquals(0, $rowCount);
    }

    /**
     * 正常系
     * トランザクションスコープによるcommitが実行できること
     * @test
     * @dataProvider transactionProvider
     */
    public function okCommitInTransactionScope($driverClassPath, $configPath)
    {
        $container = new Container();
        $container->logger = new DummyLogger();
        $config = new Container();
        $config->configPath = dirname(__FILE__) . $configPath;
        $config->driverClassPath = $driverClassPath;
        $config->filepath = "test";
        $container->connectionContainerList = [$config];

        $manager = new DatabaseManager($container);
        $manager->loadConnection($config->filepath);
        $transactionConfig = [
            'isolationLevel' => Connection::TRANSACTION_READ_COMMITTED,
            'autoCommit' => false
        ];

        $manager->connect();
        $manager->transactional(function ($conn) {
            $conn->query('INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test'])->insert();
            $conn->query('INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test'])->insert();
            $conn->query('INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test'])->insert();
            $conn->query('INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test'])->insert();
        }, $transactionConfig);
        $manager->disconnect();

        $manager = new DatabaseManager($container);
        $manager->loadConnection($config->filepath);
        $manager->connect();
        $result = $manager->query('SELECT COUNT(*) AS count FROM T_WebStream')->select();
        $rowCount = (int)$result[0]['count'];
        $manager->disconnect();

        $this->assertEquals(4, $rowCount);
    }


    /**
     * 正常系
     * トランザクションスコープによるrollbackが実行できること
     * @test
     * @dataProvider transactionProvider
     */
    public function okRollbackInTransactionScope($driverClassPath, $configPath)
    {
        $container = new Container();
        $container->logger = new DummyLogger();
        $config = new Container();
        $config->configPath = dirname(__FILE__) . $configPath;
        $config->driverClassPath = $driverClassPath;
        $config->filepath = "test";
        $container->connectionContainerList = [$config];

        $manager = new DatabaseManager($container);
        $manager->loadConnection($config->filepath);
        $manager->connect();

        try {
            $manager->transactional(function ($conn) {
                $conn->query('INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test'])->insert();
                $conn->query('INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test'])->insert();
                $conn->query('INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test'])->insert();
                $conn->query('INSERT INTO T_WebStream (name) VALUES (:name)', ['name' => 'test'])->insert();
                // force execute an exception
                throw new \Exception();
            });
        } catch (DatabaseException $e) {
            // nothing to do
        } finally {
            $manager->disconnect();
        }

        $manager = new DatabaseManager($container);
        $manager->loadConnection($config->filepath);
        $manager->connect();
        $result = $manager->query('SELECT COUNT(*) AS count FROM T_WebStream')->select();
        $rowCount = (int)$result[0]['count'];
        $manager->disconnect();

        $this->assertEquals(0, $rowCount);
    }
}
