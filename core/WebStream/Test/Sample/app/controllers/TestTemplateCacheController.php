<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Template;
use WebStream\Annotation\TemplateCache;

class TestTemplateCacheController extends CoreController
{
    /**
     * @Inject
     * @TemplateCache(expire=10)
     * @Template("index.tmpl")
     */
    public function index1()
    {
    }
}
