<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Validate;

class ValidateFixture8 implements IAnnotatable
{
    /**
     * @Validate(key="test", rule="number", method="get")
     */
    public function number1()
    {
    }

    /**
     * @Validate(key="test", rule="number", method="post")
     */
    public function number2()
    {
    }

    /**
     * @Validate(key="test", rule="number", method="put")
     */
    public function number3()
    {
    }

    /**
     * @Validate(key="test", rule="number", method="delete")
     */
    public function number4()
    {
    }
}
