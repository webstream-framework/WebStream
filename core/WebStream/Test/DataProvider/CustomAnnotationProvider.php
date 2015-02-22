<?php
namespace WebStream\Test\DataProvider;

/**
 * FilterProvider
 * @author Ryuichi TANAKA.
 * @since 2013/11/30
 * @version 0.4
 */
trait CustomAnnotationProvider
{
    public function classProvider()
    {
        return [
            ["/test_custom_class_annotation/index1", "kke"],
            ["/test_custom_class_annotation/index2", "kashikoi"]
        ];
    }

    public function methodProvider()
    {
        return [
            ["/test_custom_method_annotation/index1", "niconiconi-"],
            ["/test_custom_method_annotation/index2", "makimakima-"],
            ["/test_custom_method_annotation/index3", "chunchun"],
            ["/test_custom_method_annotation/index4", "niconiconi-"]
        ];
    }

    public function propertyProvider()
    {
        return [
            ["/test_custom_property_annotation/index1", "spiritualyane"],
            ["/test_custom_property_annotation/index2", "kke"],
            ["/test_custom_property_annotation/index3", "sanchou attack"]
        ];
    }
}
