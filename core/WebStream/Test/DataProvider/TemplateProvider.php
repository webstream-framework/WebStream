<?php
namespace WebStream\Test\DataProvider;

/**
 * TemplateProvider
 * @author Ryuichi TANAKA.
 * @since 2013/10/19
 * @version 0.4
 */
trait TemplateProvider {
    public function templateProvider()
    {
        return [
            ["/test_template/index1", "index1"],
            ["/test_template/index2", "hoge"],
            ["/test_template/index3", "honoka"],
            ["/test_template/index4", "kotori"],
            ["/test_template/index5", "nicomaki"],
            ["/test_template/index6", "printemps"]
        ];
    }

    public function templateErrorProvider()
    {
        return [
            ["/test_template/error1", 404],
            ["/test_template/error2", 404],
            ["/test_template/error3", 500],
            ["/test_template/error4", 500],
            ["/test_template/error5", 500]
        ];
    }
}
