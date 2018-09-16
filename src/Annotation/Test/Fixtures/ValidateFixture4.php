<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Validate;

class ValidateFixture4 implements IAnnotatable
{
    /**
     * @Validate(key="test", rule="max[5]", method="get")
     */
    public function max1()
    {
    }

    /**
     * @Validate(key="test", rule="max[5]", method="post")
     */
    public function max2()
    {
    }

    /**
     * @Validate(key="test", rule="max[5]", method="put")
     */
    public function max3()
    {
    }

    /**
     * @Validate(key="test", rule="max[5]", method="delete")
     */
    public function max4()
    {
    }
}
