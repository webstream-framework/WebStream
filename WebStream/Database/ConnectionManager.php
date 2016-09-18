<?php
namespace WebStream\Database;

use WebStream\Container\Container;
use WebStream\Exception\Extend\DatabaseException;

/**
 * ConnectionManager
 * @author Ryuichi TANAKA.
 * @since 2014/06/13
 * @version 0.4
 */
class ConnectionManager
{
    /**
     * @var array<string> クラスパス-DSNハッシュマップ
     */
    private $classpathMap;

    /**
     * @var AnnotationContainer データベース接続項目コンテナ
     */
    private $connectionContainer;

    /**
     * constructor
     * @param Container 依存コンテナ
     */
    public function __construct(Container $container)
    {
        $this->initialize($container);
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->connectionContainer = null;
    }

    /**
     * DBコネクションを返却する
     * @param string Modelクラスファイルパス
     * @return DatabaseDriver データベースドライバインスタンス
     */
    public function getConnection($filepath)
    {
        $dsnHash = $this->classpathMap[$filepath];

        return $dsnHash !== null ? $this->connectionContainer->{$dsnHash} : null;
    }

    /**
     * 初期処理
     * @param Container 依存コンテナ
     */
    private function initialize(Container $container)
    {
        $this->classpathMap = [];
        $this->connectionContainer = new Container();
        $logger = $container->logger;

        foreach ($container->connectionContainerList as $container) {
            $config = null;
            $ext = pathinfo($container->configPath, PATHINFO_EXTENSION);
            if ($ext === 'ini') {
                $config = parse_ini_file($container->configPath);
            } elseif ($ext === 'yml' || $ext === 'yaml') {
                $config = \Spyc::YAMLLoad($container->configPath);
            } else {
                throw new DatabaseException("Yaml or ini file only available database configuration file.");
            }

            $driverClassPath = $container->driverClassPath;

            $dsnHash = "";
            $databaseConfigContainer = new Container(false);
            foreach ($config as $key => $value) {
                $dsnHash .= $key . $value;
                $databaseConfigContainer->set($key, $value);
            }
            $dsnHash = md5($dsnHash);

            $this->classpathMap[$container->filepath] = $dsnHash;

            $this->connectionContainer->{$dsnHash} = function () use ($driverClassPath, $databaseConfigContainer, $logger) {
                $driver = new $driverClassPath($databaseConfigContainer);
                $driver->inject('logger', $logger);

                return $driver;
            };
        }
    }
}
