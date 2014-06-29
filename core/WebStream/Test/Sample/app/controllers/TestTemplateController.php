<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Autowired;
use WebStream\Annotation\Template;
use WebStream\Annotation\Filter;
use WebStream\Annotation\Header;

class TestTemplateController extends CoreController
{
    /**
     * @Inject
     * @Autowired(value="autowired value")
     */
    private $name;

    /**
     * @Inject
     * @Filter(type="before")
     */
    public function before()
    {
    }

    /**
     * @Inject
     * @Template("base1.tmpl")
     */
    public function index1()
    {
    }

    /**
     * @Inject
     * @Template("base2.tmpl")
     */
    public function index2()
    {
    }

    /**
     * @Inject
     * @Template("base3.tmpl")
     * @Template("parts1.tmpl", name="parts", type="parts")
     */
    public function index3()
    {
    }

    /**
     * @Inject
     * @Template("base4.tmpl")
     * @Template("shared1.tmpl", name="shared", type="shared")
     */
    public function index4()
    {
    }

    /**
     * @Inject
     * @Template("base5.tmpl")
     * @Template("parts2.tmpl", name="parts", type="parts")
     * @Template("shared2.tmpl", name="shared", type="shared")
     */
    public function index5()
    {
    }

    /**
     * @Inject
     * @Template("base6.tmpl")
     */
    public function index6()
    {
    }

    /**
     * @Inject
     * @Template("shared3.tmpl", type={"base","shared"})
     */
    public function index7()
    {
    }

    /**
     * @Inject
     * @Template("notfound.tmpl")
     */
    public function error1()
    {
    }

    /**
     * @Inject
     * @Template("base4.tmpl")
     * @Template("notfound.tmpl", name="shared", type="shared")
     */
    public function error2()
    {
    }

    /**
     * @Inject
     * @Template("base1.tmpl", type="hogehoge")
     */
    public function error3()
    {
    }

    /**
     * @Inject
     * @Template("base1.tmpl")
     * @Template("parts1.tmpl", type="parts");
     */
    public function error4()
    {
    }

    /**
     * @Inject
     * @Template("base1.tmpl")
     * @Template("parts1.tmpl", name="parts");
     */
    public function error5()
    {
    }

    /**
     * @Inject
     * @Template("base1.tmpl")
     * @Template("parts1.tmpl", name="model", type="shared");
     */
    public function error6()
    {
    }

    /**
     * @Inject
     * @Template("base1.tmpl")
     * @Template("parts1.tmpl", name="model", type="parts");
     */
    public function error7()
    {
    }

    /**
     * @Inject
     * @Template(name="index")
     */
    public function error8()
    {
    }

    /**
     * @Inject
     * @Header(contentType="xml")
     * @Template("xml.tmpl")
     */
    public function xml()
    {
    }

    /**
     * @Inject
     * @Template("javascript1.tmpl")
     */
    public function javascript1()
    {
    }

    /**
     * @Inject
     * @Template("javascript2.tmpl")
     */
    public function javascript2()
    {
    }

    /**
     * @Inject
     * @Template("javascript3.tmpl")
     */
    public function javascript3()
    {
    }

    /**
     * @Inject
     * @Template("javascript4.tmpl")
     */
    public function javascript4()
    {
    }

    /**
     * @Inject
     * @Template("javascript5.tmpl")
     */
    public function javascript5()
    {
    }

    /**
     * @Inject
     * @Template("javascript6.tmpl")
     */
    public function javascript6()
    {
    }

    /**
     * @Inject
     * @Template("javascript7.tmpl")
     */
    public function javascript7()
    {
    }

    /**
     * @Inject
     * @Template("javascript8.tmpl")
     */
    public function javascript8()
    {
    }

    /**
     * @Inject
     * @Template("html1.tmpl")
     */
    public function html1()
    {
    }

    /**
     * @Inject
     * @Template("php1.tmpl")
     */
    public function php1()
    {
    }

    /**
     * @Inject
     * @Filter(type="after")
     */
    public function after()
    {
    }
}
