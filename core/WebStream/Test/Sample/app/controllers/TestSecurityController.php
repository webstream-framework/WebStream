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

    /**
     * @Inject
     * @Template("csrf_post.tmpl")
     */
    public function testCsrfPost()
    {
    }

    /**
     * @Inject
     * @Template("csrf_post_view.tmpl")
     */
    public function testCsrfPostView()
    {
    }

    /**
     * @Inject
     * @Template("csrf_helper.tmpl")
     */
    public function testCsrfHelper()
    {
    }
}
