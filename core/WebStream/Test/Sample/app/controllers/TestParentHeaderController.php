<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Header;
use WebStream\Annotation\Template;

class TestParentHeaderController extends CoreController
{
    /**
     * @Inject
     * @Template("html.tmpl")
     * @Header(contentType="html")
     */
    public function test16()
    {
    }
}
