<?php
namespace WebStream\Test\TestData\Sample\App\Libraries\Validate\Invalid2;

use WebStream\Validate\Rule\IValidate;

class Invalid implements IValidate
{
    /**
     * {@inheritdoc}
     */
    public function isValid($value, $rule)
    {
        return true;
    }
}
