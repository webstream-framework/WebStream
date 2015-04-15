<?php
namespace WebStream\Test\TestData\Sample\App\Libraries\Validate;

use WebStream\Validate\Rule\IValidate;

class Mail implements IValidate
{
    /**
     * {@inheritdoc}
     */
    public function isValid($value, $rule)
    {
        $pattern = '/^(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&\'*+\\/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!'.
            '#\$\%&\'*+\\/=?\^`{}~|\-]+))*)|(?:"(?:\\[^\r\n]|[^\\"])*")))\@(?:(?:(?:(?:'.
            '[a-zA-Z0-9_!#\$\%&\'*+\\/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&\'*+\\/=?\^`'.
            "{}~|\-]+))*)|(?:\[(?:\\\S|[\x21-\x5a\x5e-\x7e])*\])))$/";

        return $value === null || (bool) preg_match($pattern, $value);
    }
}
