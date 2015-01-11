<?php
namespace WebStream\Test\DataProvider;

/**
 * IteratorProvider
 * @author Ryuichi TANAKA.
 * @since 2014/07/02
 * @version 0.4
 */
trait IteratorProvider
{
    public function iteratorProvider()
    {
        return [
            ["/test_iterator1", "1"],
            ["/test_iterator2", "kotori"],
            ["/test_iterator3", "OutOfBoundsException"],
            ["/test_iterator4", "0honoka1kotori"],
            ["/test_iterator5", "kotori"],
            ["/test_iterator6", "WebStream\Exception\Extend\CollectionException"]
        ];
    }
}
