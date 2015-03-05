<?php
namespace WebStream\Test\DataProvider;

/**
 * CustomAnnotationProvider
 * @author Ryuichi TANAKA.
 * @since 2015/03/03
 * @version 0.4
 */
trait CustomAnnotationProvider
{
    public function classProvider()
    {
        return [
            ["/test_custom_class_annotation/index1", "kke"],
            ["/test_custom_class_annotation/index2", "kashikoi"],
            ["/test_custom_class_annotation/service1", "kkekkeservice"],
            ["/test_custom_class_annotation/service2", "kashikoiservice"],
            ["/test_custom_class_annotation/model1", "kkekkekkemodel"],
            ["/test_custom_class_annotation/model2", "kashikoimodel"]
        ];
    }

    public function methodProvider()
    {
        return [
            ["/test_custom_method_annotation/index1", "niconiconi-"],
            ["/test_custom_method_annotation/index2", "makimakima-"],
            ["/test_custom_method_annotation/index3", "chunchun"],
            ["/test_custom_method_annotation/index4", "niconiconi-"],
            ["/test_custom_method_annotation/service1", "niconiconi-"],
            ["/test_custom_method_annotation/service2", "makimakima-"],
            ["/test_custom_method_annotation/service3", "chunchun"],
            ["/test_custom_method_annotation/service4", "niconiconiconiconi-"],
            ["/test_custom_method_annotation/model1", "niconiconi-"],
            ["/test_custom_method_annotation/model2", "makimakima-"],
            ["/test_custom_method_annotation/model3", "chunchun"],
            ["/test_custom_method_annotation/model4", "niconiconiconiconiconiconi-"]
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
