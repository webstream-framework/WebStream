<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Template;

class TestViewModelInServiceController extends CoreController
{
    /**
     * @Inject
     * @Template("service1.tmpl")
     */
    public function service1()
    {
        $this->TestViewModelInService->service1();
    }

    /**
     * @Inject
     * @Template("service2.tmpl")
     */
    public function service2()
    {
        $this->TestViewModelInService->service2();
    }
}
