<?php
namespace WebStream\Database;

use WebStream\Module\Container;
use WebStream\Annotation\Container\AnnotationListContainer;
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
     * @param AnnotationContainer データベース接続項目コンテナ
     */
    public function __construct(AnnotationListContainer $connectionItemContainerList)
    {
        $this->initialize($connectionItemContainerList);
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
     * @param AnnotationContainer データベース接続項目コンテナ
     */
    private function initialize(AnnotationListContainer $connectionItemContainerList)
    {
        $this->classpathMap = [];
        $this->connectionContainer = new Container();

        foreach ($connectionItemContainerList as $container) {
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
            foreach ($config as $key => $value) {
                $dsnHash .= $key . $value;
            }
            $dsnHash = md5($dsnHash);

            $this->classpathMap[$container->filepath] = $dsnHash;

            $this->connectionContainer->{$dsnHash} = function () use ($config, $driverClassPath) {
                $driver = new $driverClassPath();

                if (array_key_exists("host", $config)) {
                    $driver->setHost($config["host"]);
                }
                if (array_key_exists("port", $config)) {
                    $driver->setPort($config["port"]);
                }
                if (array_key_exists("dbname", $config)) {
                    $driver->setDbname($config["dbname"]);
                }
                if (array_key_exists("username", $config)) {
                    $driver->setUsername($config["username"]);
                }
                if (array_key_exists("password", $config)) {
                    $driver->setPassword($config["password"]);
                }
                if (array_key_exists("dbfile", $config)) {
                    $driver->setDbfile($config["dbfile"]);
                }

                return $driver;
            };
        }
    }
}
