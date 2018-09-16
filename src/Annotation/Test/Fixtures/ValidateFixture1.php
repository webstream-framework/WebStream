<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Validate;

class ValidateFixture1 implements IAnnotatable
{
    /**
     * @Validate(key="test", rule="required", method="get")
     */
    public function required1()
    {
    }

    /**
     * @Validate(key="test", rule="required", method="post")
     */
    public function required2()
    {
    }

    /**
     * @Validate(key="test", rule="required", method="put")
     */
    public function required3()
    {
    }

    /**
     * @Validate(key="test", rule="required", method="delete")
     */
    public function required4()
    {
    }
}
