<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Header;

class HeaderFixture1 implements IAnnotatable
{
    /**
     * @Header(allowMethod="post")
     */
    public function action1()
    {
    }

    /**
     * @Header(allowMethod="POST")
     */
    public function action2()
    {
    }

    /**
     * @Header(allowMethod={"get", "post"})
     */
    public function action3()
    {
    }

    /**
     * @Header(allowMethod="undefined")
     */
    public function action4()
    {
    }

    /**
     * @Header(contentType="xml")
     */
    public function action5()
    {
    }

    /**
     * @Header(contentType="undefined")
     */
    public function action6()
    {
    }

    /**
     * @Header(contentType={"html", "xml"})
     */
    public function action7()
    {
    }
}
