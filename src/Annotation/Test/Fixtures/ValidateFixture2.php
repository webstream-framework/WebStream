<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Validate;

class ValidateFixture2 implements IAnnotatable
{
    /**
     * @Validate(key="test", rule="equal[value]", method="get")
     */
    public function equal1()
    {
    }

    /**
     * @Validate(key="test", rule="equal[value]", method="post")
     */
    public function equal2()
    {
    }

    /**
     * @Validate(key="test", rule="equal[value]", method="put")
     */
    public function equal3()
    {
    }

    /**
     * @Validate(key="test", rule="equal[value]", method="delete")
     */
    public function equal4()
    {
    }
}
