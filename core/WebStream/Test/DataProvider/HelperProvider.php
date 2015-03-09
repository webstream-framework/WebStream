<?php
namespace WebStream\Test\DataProvider;

/**
 * HelperProvider
 * @author Ryuichi TANAKA.
 * @since 2013/12/05
 * @version 0.4
 */
trait HelperProvider
{
    public function helperProvider()
    {
        return [
            ["/test_helper1", "erichika"],
            ["/test_helper2", "μ&#039;s"],
            ["/test_helper3", "LilyWhite"],
            ["/test_helper4", "BiBi"],
            ["/test_helper5", "&lt;script&gt;alert(&quot;xss&quot;);&lt;/script&gt;"],
            ["/test_helper6", "honoka16"]
        ];
    }
}
