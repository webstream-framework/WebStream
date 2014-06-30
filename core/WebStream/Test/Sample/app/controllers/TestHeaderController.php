<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Header;
use WebStream\Annotation\Template;

class TestHeaderController extends TestParentHeaderController
{
    /**
     * @Inject
     * @Template("html.tmpl")
     * @Header(contentType="html")
     */
    public function test1()
    {
    }

    /**
     * @Inject
     * @Template("xml.tmpl")
     * @Header(contentType="xml")
     */
    public function test2()
    {
    }

    /**
     * @Inject
     * @Template("atom.tmpl")
     * @Header(contentType="atom")
     */
    public function test3()
    {
    }

    /**
     * @Inject
     * @Template("rss.tmpl")
     * @Header(contentType="rss")
     */
    public function test4()
    {
    }

    /**
     * @Inject
     * @Template("rdf.tmpl")
     * @Header(contentType="rdf")
     */
    public function test5()
    {
    }

    /**
     * @Inject
     * @Header(allowMethod="get")
     */
    public function test6()
    {
    }

    /**
     * @Inject
     * @Header(allowMethod="GET")
     */
    public function test7()
    {
    }

    /**
     * @Inject
     * @Header(allowMethod="post")
     */
    public function test8()
    {
    }

    /**
     * @Inject
     * @Header(allowMethod="POST")
     */
    public function test9()
    {
    }

    /**
     * @Inject
     * @Header(allowMethod={"GET","POST"})
     */
    public function test10()
    {
    }

    /**
     * @Inject
     * @Header(allowMethod={"POST","PUT"})
     */
    public function test11()
    {
    }

    /**
     * @Inject
     * @Header(contentType="html", allowMethod="GET")
     */
    public function test12()
    {
    }

    /**
     * @Inject
     * @Header(contentType="xml", allowMethod="POST")
     */
    public function test13()
    {
    }

    /**
     * @Inject
     * @Header(contentType="dummy")
     */
    public function test14()
    {
    }

    /**
     * @Inject
     * @Header(allowMethod="dummy")
     */
    public function test15()
    {
    }
}
