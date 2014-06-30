<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Inject;
use WebStream\Annotation\TemplateCache;

class TemplateCacheTest1
{
    /**
     * @Inject
     * @TemplateCache(expire="100")
     */
    public function index()
    {
    }

    /**
     * @Inject
     * @TemplateCache(expire="9223372036854775808")
     */
    public function index2()
    {
    }

    /**
     * @Inject
     * @TemplateCache(expire="-100")
     */
    public function error1()
    {
    }

    /**
     * @Inject
     * @TemplateCache(expire="aaa")
     */
    public function error2()
    {
    }
}
