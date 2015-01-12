<?php
namespace WebStream\Database;

use WebStream\Module\Utility;
use WebStream\Module\Logger;

/**
 * EntityManager
 * @author Ryuichi TANAKA.
 * @since 2015/01/11
 * @version 0.4
 */
class EntityManager
{
    use Utility;

    /**
     * @var string エンティティクラスパス
     */
    private $classpath;

    /**
     * コンストラクタ
     * @param string エンティティクラスパス
     */
    public function __construct($classpath)
    {
        $this->classpath = $classpath;
    }

    /**
     * 列データをエンティティに変換して返却する
     * @param array<string> 列データ
     * @return object 列データエンティティ
     */
    public function getEntity($row)
    {
        $instance = new $this->classpath();
        $refClass = new \ReflectionClass($this->classpath);
        $properties = $refClass->getProperties();

        $propertyMap = [];
        foreach ($properties as $property) {
            if ($property->isPrivate() || $property->isProtected()) {
                $property->setAccessible(true);
            }
            $propertyMap[strtolower($property->getName())] = $property;
        }

        foreach ($row as $col => $value) {
            $col = strtolower($this->snake2lcamel($col));
            if (array_key_exists($col, $propertyMap)) {
                $propertyMap[$col]->setValue($instance, $value);
            } else {
                Logger::error("Column '$col' is failed mapping in " . $this->classpath);
            }
        }

        return $instance;
    }
}
