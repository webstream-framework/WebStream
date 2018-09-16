<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Validate;

class ValidateFixture3 implements IAnnotatable
{
    /**
     * @Validate(key="test", rule="length[5]", method="get")
     */
    public function length1()
    {
    }

    /**
     * @Validate(key="test", rule="length[5]", method="post")
     */
    public function length2()
    {
    }

    /**
     * @Validate(key="test", rule="length[5]", method="put")
     */
    public function length3()
    {
    }

    /**
     * @Validate(key="test", rule="length[5]", method="delete")
     */
    public function length4()
    {
    }
}
