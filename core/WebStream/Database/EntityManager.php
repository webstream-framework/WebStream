<?php
namespace WebStream\Database;

use WebStream\Module\Utility;

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
        foreach ($properties as $property) {
            if ($property->isPrivate() || $property->isProtected()) {
                $property->setAccessible(true);
            }
            // カラム名(スネークケース) -> フィールド名(キャメルケース)
            $property->setValue($instance, $row[$this->snake2lcamel($property->getName())]);
        }

        return $instance;
    }
}
