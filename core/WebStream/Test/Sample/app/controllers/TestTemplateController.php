<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Autowired;
use WebStream\Annotation\Template;
use WebStream\Annotation\Filter;
use WebStream\Annotation\Value;
use WebStream\Annotation\Type;

class TestTemplateController extends CoreController
{
    /**
     * @Autowired
     * @Value("autowired value")
     */
    private $name;

    /**
     * @Inject
     * @Filter("Before")
     */
    public function before()
    {
    }

    /**
     * @Inject
     * @Template("index.tmpl")
     */
    public function index1()
    {
    }

    /**
     * @Inject
     * @Template("index.tmpl")
     */
    public function index2()
    {
    }

    /**
     * @Inject
     * @Filter("After")
     */
    public function after()
    {
    }
}
