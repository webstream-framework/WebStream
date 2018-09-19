<?php
namespace WebStream\Test\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Attributes\Validate;

class ValidateTestController extends CoreController
{
    /**
     * @Validate(key="test", rule="required", method="get")
     */
    public function required()
    {
        echo $this->request->get['test'];
    }
}
