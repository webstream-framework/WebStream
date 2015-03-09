<?php
namespace WebStream\Test\TestData\Sample\App\Entity;

class QueryEntity6
{
    use QueryEntityTrait1;

    use QueryEntityTrait2 {
        QueryEntityTrait2::getName insteadof QueryEntityTrait1;
    }
}
