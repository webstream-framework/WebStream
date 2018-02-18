<?php
namespace WebStream\Test\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Attributes\Alias;
use WebStream\Annotation\Attributes\ExceptionHandler;
use WebStream\Annotation\Attributes\Filter;
use WebStream\Annotation\Attributes\Header;
use WebStream\Annotation\Attributes\Template;
use WebStream\Annotation\Attributes\Validate;
use WebStream\Annotation\Attributes\Custom\CustomControllerAnnotation;
use WebStream\Exception\Extend\ForbiddenAccessException;

class Test1Controller extends CoreController
{
    /**
     * @Filter(type="before")
     */
    public function before()
    {
        echo "b1";
    }

    /**
     * @Filter(type="after")
     */
    public function after()
    {
        echo "a1";
    }

    /**
     * @Template("test1.tmpl")
     */
    public function test1()
    {
    }

    public function test2()
    {
        $this->Test1->test1();
    }

    /**
     * @Header(allowMethod="post")
     */
    public function test3()
    {
    }

    public function test4()
    {
        throw new ForbiddenAccessException("error");
    }

    /**
     * @ExceptionHandler("WebStream\Exception\Extend\ForbiddenAccessException")
     */
    public function test4Error($params)
    {
        echo $params['method'];
    }

    /**
     * @Alias(name="test5")
     */
    public function test5Alias()
    {
        echo "t5";
    }

    /**
     * @Validate(key="test", rule="required", method="get")
     */
    public function test6()
    {
        var_dump($this->request);
    }

    public function test9()
    {

    }

    /**
     * @CustomControllerAnnotation(type="custom")
     */
    public function test10()
    {
    }
}
