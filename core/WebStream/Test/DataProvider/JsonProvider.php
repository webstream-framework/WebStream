<?php
namespace WebStream\Test\DataProvider;

/**
 * JsonProvider
 * @author Ryuichi TANAKA.
 * @since 2014/05/04
 * @version 0.4
 */
trait JsonProvider
{
    public function jsonProvider()
    {
        return [
            ["/test_json1", "{\"name\":\"<>'\\\"\"}"],
            ["/test_json2", "{\"name\":\"\u003C\u003E\u0027\u0022\"}"]
        ];
    }

    public function jsonpProvider()
    {
        return [
            ["/test_jsonp1", "callback({\"name\":\"<>'\\\"\"});"],
            ["/test_jsonp2", "callback({\"name\":\"\u003C\u003E\u0027\u0022\"});"]
        ];
    }
}
