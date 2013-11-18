<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;

class TestValidatorController extends CoreController
{
    public function getRequired()
    {
        echo $this->request->get("name");
    }

    public function getMinLength()
    {
        echo $this->request->get("name");
    }

    public function getMaxLength()
    {
        echo $this->request->get("name");
    }

    public function getMin()
    {
        echo $this->request->get("num");
    }

    public function getMax()
    {
        echo $this->request->get("num");
    }

    public function getEqual()
    {
        echo $this->request->get("name");
    }

    public function getLength()
    {
        echo $this->request->get("name");
    }

    public function getRange()
    {
        echo $this->request->get("num");
    }

    public function getRegexp()
    {
        echo $this->request->get("num");
    }

    public function getNumber()
    {
        echo $this->request->get("num");
    }

    public function getDouble()
    {
        echo $this->request->get("num");
    }
}
