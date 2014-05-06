<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Exception\Extend\CollectionException;

class TestController extends CoreController
{
    public function test1()
    {
        echo "test1";
    }

    public function test2()
    {
        echo "test2";
    }

    public function test3($params)
    {
        echo $params["id"];
    }

    public function testAction()
    {
        echo "testAction";
    }

    public function testActionHogeFuga()
    {
        echo "testAction2";
    }

    public function testFeed($params)
    {
        echo $params["format"];
    }

    public function testEncoded($params)
    {
        echo $params["name"];
    }

    public function testSimilar1()
    {
        echo "similar1";
    }

    public function testSimilar2($params)
    {
        echo "similar". $params["page"];
    }

    public function service1()
    {
        echo $this->Test->service1();
    }

    public function service2()
    {
        echo $this->Test->service2();
    }

    public function hoge()
    {
        throw new CollectionException("owata!");
    }
}
