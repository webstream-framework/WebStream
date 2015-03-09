<?php
namespace WebStream\Test\TestData\Sample\App\Entity;

trait QueryEntityTrait2
{
    private $id;

    private $name;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
}
