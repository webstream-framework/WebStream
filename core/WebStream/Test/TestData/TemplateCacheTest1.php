<?php
namespace WebStream\Test\TestData;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Inject;
use WebStream\Annotation\TemplateCache;
use WebStream\Module\Container;

class TemplateCacheTest1 implements CoreInterface
{
    public function __construct(Container $container) {}

    public function __destruct() {}

    /**
     * @Inject
     * @TemplateCache(expire="100")
     */
    public function index1()
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
