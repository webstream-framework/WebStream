<?php
namespace WebStream\Database;

use WebStream\Module\Container;
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
     * @param array<AnnotationContainer> データベース接続項目コンテナ
     */
    public function __construct(array $connectionItemContainerList)
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
     * @param array<AnnotationContainer> データベース接続項目コンテナ
     */
    private function initialize(array $connectionItemContainerList)
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
            $databaseConfigContainer = new Container(false);
            foreach ($config as $key => $value) {
                $dsnHash .= $key . $value;
                $databaseConfigContainer->set($key, $value);
            }
            $dsnHash = md5($dsnHash);

            $this->classpathMap[$container->filepath] = $dsnHash;

            $this->connectionContainer->{$dsnHash} = function () use ($driverClassPath, $databaseConfigContainer) {
                return new $driverClassPath($databaseConfigContainer);
            };
        }
    }
}
