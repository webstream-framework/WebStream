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

    public function postRequired()
    {
        echo $this->request->post("name");
    }

    public function postMinLength()
    {
        echo $this->request->post("name");
    }

    public function postMaxLength()
    {
        echo $this->request->post("name");
    }

    public function postMin()
    {
        echo $this->request->post("num");
    }

    public function postMax()
    {
        echo $this->request->post("num");
    }

    public function postEqual()
    {
        echo $this->request->post("name");
    }

    public function postLength()
    {
        echo $this->request->post("name");
    }

    public function postRange()
    {
        echo $this->request->post("num");
    }

    public function postRegexp()
    {
        echo $this->request->post("num");
    }

    public function postNumber()
    {
        echo $this->request->post("num");
    }

    public function postDouble()
    {
        echo $this->request->post("num");
    }

    public function putRequired()
    {
        echo $this->request->put("name");
    }

    public function putMinLength()
    {
        echo $this->request->put("name");
    }

    public function putMaxLength()
    {
        echo $this->request->put("name");
    }

    public function putMin()
    {
        echo $this->request->put("num");
    }

    public function putMax()
    {
        echo $this->request->put("num");
    }

    public function putEqual()
    {
        echo $this->request->put("name");
    }

    public function putLength()
    {
        echo $this->request->put("name");
    }

    public function putRange()
    {
        echo $this->request->put("num");
    }

    public function putRegexp()
    {
        echo $this->request->put("num");
    }

    public function putNumber()
    {
        echo $this->request->put("num");
    }

    public function putDouble()
    {
        echo $this->request->put("num");
    }
}
