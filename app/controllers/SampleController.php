<?php
namespace WebStream\Sample;

use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;
use WebStream\Annotation\Template;
use WebStream\Core\CoreController;

/**
 * SampleController
 */
class SampleController extends CoreController
{
    private $title;

    /**
     * @Inject
     * @Filter(type="before")
     */
    public function before()
    {
        $this->title = "sample title";
    }

    /**
     * @Inject
     * @Filter(type="after")
     */
    public function after() {}

    /**
     * @Inject
     * @Template("base.tmpl", type={"base","shared"})
     * @Template("index.tmpl", name="index", type="parts")
     */
    public function index($params)
    {
        $this->Sample->setTitle($this->title);
    }

    /**
     * @Inject
     * @Template("model1.tmpl")
     */
    public function model1()
    {
        $this->Sample->setDescription();
    }
}
