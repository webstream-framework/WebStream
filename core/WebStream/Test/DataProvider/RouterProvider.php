<?php
namespace WebStream\Test\DataProvider;

/**
 * RouterProvider
 * @author Ryuichi TANAKA.
 * @since 2013/09/21
 * @version 0.4
 */
trait RouterProvider
{
    // 正常系
    public function resolvePathWithoutPlaceHolderProvider()
    {
        return [
            ["/", "test#test1", "test1"],
            ["/top", "test#test2", "test2"]
        ];
    }

    public function resolvePathWithPlaceHolderProvider()
    {
        return [
            ['/top/:id', "test#test3", "test3"]
        ];
    }

    public function resolveCamelActionProvider()
    {
        return [
            ['/action', "testAction"],
            ['/action2', "testAction2"]
        ];
    }

    public function resolveWithPlaceHolderFormatProvider()
    {
        return [
            ['/feed.rss']
        ];
    }

    public function snakeControllerProvider()
    {
        return [
            ["/snake", "snake"],
            ["/snake2", "snake2"]
        ];
    }

    public function uriWithEncodedStringProvider()
    {
        return [
            ['/encoded/%E3%81%A6%E3%81%99%E3%81%A8', 'てすと']
        ];
    }

    public function resolveSimilarUrlProvider()
    {
        return [
            ["/similar/name", "similar1"],
            ["/similar/name/2", "similar2"]
        ];
    }

    public function readStaticFileProvider()
    {
        return [
            ['/css/sample.css', "text/css"],
            ['/css/sample2.CSS', "text/css"],
            ['/js/sample.js', "text/javascript"],
            ['/js/sample2.JS', "text/javascript"],
            ['/img/sample.png', "image/png"],
            ['/img/sample2.PNG', "image/png"]
        ];
    }

    public function downloadStaticFile()
    {
        return [
            ['/file/sample.atom', "application/atom+xml"],
            ['/file/sample.htm', "text/html"],
            ['/file/sample.html', "text/html"],
            ['/file/sample.json', "application/json"],
            ['/file/sample.pdf', "application/pdf"],
            ['/file/sample.php', "application/octet-stream"],
            ['/file/sample.rdf', "application/rdf+xml"],
            ['/file/sample.txt', "text/plain"],
            ['/file/sample.xml', "application/xml"],
            ['/file/sample.csv', "text/csv"],
            ['/file/sample.tsv', "text/tab-separated-values"]
        ];
    }

    public function readCustomStaticFile()
    {
        return [
            ['/custom/sample.txt', "text/plain"]
        ];
    }

    // 異常系
    public function resolveUnknownProvider()
    {
        return [
            ['/notfound/controller', "notfound#test"],
            ['/notfound/action', "test#notfound"]
        ];
    }

    public function multipleSnakeControllerProvider()
    {
        return [
            ["/snake_ng1"],
            ["/snake_ng2"]
        ];
    }

}
