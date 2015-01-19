<?php
namespace WebStream\Test\TestData\Sample\App\Entity;

class QueryEntity5
{
    private $id;
    private $name;
    private $createdAt;
    private $createdAtTime;
    private $createdAtDate;
    private $bigintNum;
    private $smallintNum;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getCreatedAtTime()
    {
        return $this->createdAtTime;
    }

    public function getCreatedAtDate()
    {
        return $this->createdAtDate;
    }

    public function getBigintNum()
    {
        return $this->bigintNum;
    }

    public function getSmallintNum()
    {
        return $this->smallintNum;
    }
}
