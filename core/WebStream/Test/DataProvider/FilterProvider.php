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

    public function filterExceptOnlyProvider()
    {
        return [
            ["/before_filter_except_enable", "i1a"],
            ["/before_filter_except_disable", "bi2a"],
            ["/after_filter_except_enable", "bi3"],
            ["/after_filter_except_disable", "bi4a"],
            ["/before_filter_only_enable", "bi1"],
            ["/before_filter_only_disable", "i2"],
            ["/after_filter_only_enable", "i3a"],
            ["/after_filter_only_disable", "i4"],
            ["/before_filter_multiple_except_enable", "i1a"],
            ["/before_filter_multiple_except_enable2", "i2a"],
            ["/after_filter_multiple_except_enable", "bi3"],
            ["/after_filter_multiple_except_enable2", "bi4"],
            ["/before_filter_multiple_only_enable", "bi1"],
            ["/before_filter_multiple_only_enable2", "bi2"],
            ["/after_filter_multiple_only_enable", "i3a"],
            ["/after_filter_multiple_only_enable2", "i4a"]
        ];
    }

    public function filterSkipProvider()
    {
        return [
            ["/skip_filter1", "b2i"],
            ["/skip_filter2", "i"]
        ];
    }

    public function filterExceptAndOnlyProvider()
    {
        return [
            ["/filter_except_only", "i"]
        ];
    }
}
