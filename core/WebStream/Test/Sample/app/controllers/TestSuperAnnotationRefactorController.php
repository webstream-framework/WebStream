<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Header;
use WebStream\Annotation\Filter;

class TestSuperAnnotationRefactorController extends CoreController
{
    private $name;

    /**
     * @Inject
     * @Filter(type="before", only={"index2", "index3"})
     */
    public function before()
    {
        var_dump("before");
    }

    /**
     * @Inject
     * @Filter(type="before")
     */
    public function before2()
    {
        var_dump("before2");
    }

    /**
     * @Inject
     * @Filter(type="after", except="index1")
     */
    public function after()
    {
        var_dump("after");
    }

    /**
     * @Inject
     * @Filter(type="before")
     */
    public function before3()
    {
        $this->name = "ﾎﾉｶﾁｬｰﾝ";
        var_dump("before3");
    }

    /**
     * @Inject
     * @Filter(type="after", only="index1")
     */
    public function after2()
    {
        var_dump($this->name);
        var_dump("after");
    }

    /**
     * @Inject
     * @Filter(type="before")
     */
    public function auth()
    {
        var_dump("auth");
    }

    /**
     * @Inject
     * @Header(contentType="html", allowMethod={"GET","POST"})
     */
    public function index2()
    {
        var_dump("index2");
    }
}
