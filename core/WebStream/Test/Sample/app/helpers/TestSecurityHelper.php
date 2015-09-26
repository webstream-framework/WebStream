<?php
namespace WebStream\Test\TestData\Sample\App\Helper;

use WebStream\Core\CoreHelper;

class TestSecurityHelper extends CoreHelper
{
    public function csrf()
    {
        return <<< HELPER
<form action="/" method="post">
</form>
HELPER;
    }
}
