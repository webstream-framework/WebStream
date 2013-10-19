<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Template;

class TestSecurityController extends CoreController
{
    /**
     * @Inject
     * @Template("index.tmpl")
     */
    public function testCsrf()
    {
    }

    /**
     * @Inject
     * @Template("csrf_get.tmpl")
     */
    public function testCsrfGet()
    {
    }
}
