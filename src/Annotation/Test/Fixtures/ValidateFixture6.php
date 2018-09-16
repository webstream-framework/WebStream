<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Validate;

class ValidateFixture6 implements IAnnotatable
{
    /**
     * @Validate(key="test", rule="max_length[5]", method="get")
     */
    public function maxLength1()
    {
    }

    /**
     * @Validate(key="test", rule="max_length[5]", method="post")
     */
    public function maxLength2()
    {
    }

    /**
     * @Validate(key="test", rule="max_length[5]", method="put")
     */
    public function maxLength3()
    {
    }

    /**
     * @Validate(key="test", rule="max_length[5]", method="delete")
     */
    public function maxLength4()
    {
    }
}
