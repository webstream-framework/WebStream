<?php
namespace WebStream\Test\TestData\Sample\App\Entity;

class QueryEntity2
{
    private $camelcaseCol;
    private $snakecaseCol;
    private $ucamelcaseCol;

    public function getValue1()
    {
        return $this->camelcaseCol;
    }

    public function getValue2()
    {
        return $this->snakecaseCol;
    }

    public function getValue3()
    {
        return $this->ucamelcaseCol;
    }
}
