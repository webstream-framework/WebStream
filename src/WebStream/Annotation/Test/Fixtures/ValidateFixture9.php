<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Validate;

class ValidateFixture9 implements IAnnotatable
{
    /**
     * @Validate(key="test", rule="range[1..5]", method="get")
     */
    public function range1()
    {
    }

    /**
     * @Validate(key="test", rule="range[1..5]", method="post")
     */
    public function range2()
    {
    }

    /**
     * @Validate(key="test", rule="range[1..5]", method="put")
     */
    public function range3()
    {
    }

    /**
     * @Validate(key="test", rule="range[1..5]", method="delete")
     */
    public function range4()
    {
    }
}
