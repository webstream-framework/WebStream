<?php
namespace WebStream\Test\TestData;

trait ClassLoaderTestTrait
{
    public function getName()
    {
        return "hoge";
    }
}

class ClassLoaderTestTraitClass
{
    use ClassLoaderTestTrait;
}
