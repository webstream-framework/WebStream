<?php
namespace WebStream\Database;

/**
 * ResultEntity
 * @author Ryuichi TANAKA.
 * @since 2015/01/11
 * @version 0.4
 */
class ResultEntity extends Result
{
    /**
     * @var EntityManager エンティティマネージャ
     */
    private $entityManager;

    /**
     * コンストラクタ
     * @param PDOStatement ステートメントオブジェクト
     * @param string エンティティクラスパス
     */
    public function __construct(\PDOStatement $stmt, $classpath)
    {
        parent::__construct($stmt);
        $this->entityManager = new EntityManager($classpath);
    }

    /**
     * Implements Iterator#current
     * 現在の要素を返却する
     * @return array<string> 列データ
     */
    public function current()
    {
        if ($this->stmt === null) {
            return array_key_exists($this->position, $this->rowCache) ? $this->rowCache[$this->position] : null;
        }

        return $this->entityManager->getEntity($this->row);
    }

    // /**
    //  * Implements Iterator#next
    //  * 次の要素に進む
    //  */
    // public function next()
    // {
    //     parent::next();
    //     $this->row = $this->entityManager->getEntity($this->row);

    //     // if ($this->stmt !== null) {
    //     //     $row = $this->stmt->fetch(\PDO::FETCH_ASSOC);
    //     //     var_dump($this->row);
    //     //     $this->row = $this->entityManager->getEntity($row);
    //     // }
    //     // $this->position++;
    // }

    // /**
    //  * Implements Iterator#rewind
    //  * イテレータを先頭に巻き戻す
    //  */
    // public function rewind()
    // {
    //     parent::rewind();
    //     $this->row = $this->entityManager->getEntity($this->row);

    //     // if ($this->stmt !== null) {
    //     //     $this->row = $this->entityManager->getEntity($this->stmt->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_FIRST));
    //     // }
    //     // $this->position = 0;
    // }
}
