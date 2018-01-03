<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Template;

class TemplateFixture1 implements IAnnotatable
{
    /**
     * @Template("test.tmpl")
     */
    public function action1()
    {
    }

    /**
     * @Template("test.tmpl", engine="basic")
     */
    public function action2()
    {
    }

    /**
     * @Template("test.tmpl", engine="twig")
     */
    public function action3()
    {
    }

    /**
     * @Template("test.tmpl", engine="twig", debug=true)
     */
    public function action4()
    {
    }

    /**
     * @Template("test.tmpl", cacheTime=10)
     */
    public function action5()
    {
    }

    /**
     * @Template("test.tmpl", engine="undefined")
     */
    public function action6()
    {
    }

    /**
     * @Template("test.tmpl", engine="twig", debug="undefined")
     */
    public function action7()
    {
    }

    /**
     * @Template("test.tmpl", engine="basic", cacheTime="undefined")
     */
    public function action8()
    {
    }

    /**
     * @Template
     */
    public function action9()
    {
    }
}
