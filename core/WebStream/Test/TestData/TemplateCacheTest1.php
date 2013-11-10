<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Inject;
use WebStream\Annotation\TemplateCache;

class TemplateCacheTest1
{
    /**
     * @Inject
     * @TemplateCache(expire=100)
     */
    public function index()
    {
    }

    /**
     * @Inject
     * @TemplateCache(expire=-100)
     */
    public function error1()
    {
    }

    /**
     * @Inject
     * @TemplateCache(expire=aaa)
     */
    public function error2()
    {
    }
}
