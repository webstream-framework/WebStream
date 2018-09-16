<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Validate;

class ValidateFixture7 implements IAnnotatable
{
    /**
     * @Validate(key="test", rule="min_length[5]", method="get")
     */
    public function minLength1()
    {
    }

    /**
     * @Validate(key="test", rule="min_length[5]", method="post")
     */
    public function minLength2()
    {
    }

    /**
     * @Validate(key="test", rule="min_length[5]", method="put")
     */
    public function minLength3()
    {
    }

    /**
     * @Validate(key="test", rule="min_length[5]", method="delete")
     */
    public function minLength4()
    {
    }
}
