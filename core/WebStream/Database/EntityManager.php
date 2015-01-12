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
     * @var array<string> カラムメタ情報
     */
    private $columnMeta;

    /**
     * コンストラクタ
     * @param string エンティティクラスパス
     */
    public function __construct($classpath)
    {
        $this->classpath = $classpath;
    }

    /**
     * カラムメタ情報を設定する
     * @param string カラムメタ情報
     */
    public function setColumnMeta(array $columnMeta)
    {
        $this->columnMeta = $columnMeta;
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
            switch ($this->columnMeta[$col]) {
                case 'LONG':      // mysql:int
                case 'SHORT':     // mysql:smallint
                case 'int4':      // postgres:int
                case 'int2':      // postgres:smallint
                case 'integer':   // sqlite:int
                case 'smallint':  // sqlite:smallint
                    $value = intval($value);
                    break;
                case 'LONGLONG':  // mysql:bigint
                case 'int8':      // postgres:bigint
                case 'bigint':    // sqlite:bigint
                    $value = doubleval($value);
                    break;
                case 'DATETIME':  // mysql:datetime
                case 'DATE':      // mysql:date
                case 'timestamp': // postgres:timestamp, sqlite:timestamp
                case 'date':      // postgres:date, sqlite:date
                    $value = new \DateTime($value);
                    break;
                default: // string
                    break;
            }

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
