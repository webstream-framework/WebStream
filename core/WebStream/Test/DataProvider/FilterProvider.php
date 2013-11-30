<?php
namespace WebStream\Test\DataProvider;

/**
 * FilterProvider
 * @author Ryuichi TANAKA.
 * @since 2013/11/30
 * @version 0.4
 */
trait FilterProvider
{
    public function filterProvider()
    {
        return [
            ["/before_after_filter", "bia"],
            ["/before_after_multiple_filter", "b1b2ia1a2"],
            ["/before_after_override_filter", "b1b2ia1a2"]
        ];
    }
}
