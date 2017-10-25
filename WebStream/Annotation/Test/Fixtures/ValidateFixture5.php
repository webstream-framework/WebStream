<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Validate;

class ValidateFixture5 implements IAnnotatable
{
    /**
     * @Validate(key="test", rule="min[3]", method="get")
     */
    public function min1()
    {
    }

    /**
     * @Validate(key="test", rule="min[3]", method="post")
     */
    public function min2()
    {
    }

    /**
     * @Validate(key="test", rule="min[3]", method="put")
     */
    public function min3()
    {
    }

    /**
     * @Validate(key="test", rule="min[3]", method="delete")
     */
    public function min4()
    {
    }
}
