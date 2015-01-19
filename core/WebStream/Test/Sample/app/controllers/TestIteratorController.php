<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;

class TestIteratorController extends CoreController
{
    public function count()
    {
        $this->TestIterator->clear();
        $this->TestIterator->add("honoka");
        $result = $this->TestIterator->model1(1);
        echo count($result);
    }

    public function seek()
    {
        $this->TestIterator->clear();
        $this->TestIterator->add("honoka");
        $this->TestIterator->add("kotori");
        $result = $this->TestIterator->model1(2);

        // kotori
        echo $result->seek(1)["name"];
    }

    public function seekFailure()
    {
        $this->TestIterator->clear();
        $this->TestIterator->add("honoka");
        $this->TestIterator->add("kotori");
        $result = $this->TestIterator->model1(2);

        // OutOfBoundException
        try {
            $result->seek(2);
        } catch (\Exception $e) {
            echo get_class($e);
        }
    }

    public function keyValue()
    {
        $this->TestIterator->clear();
        $this->TestIterator->add("honoka");
        $this->TestIterator->add("kotori");
        $result = $this->TestIterator->model1(2);

        $keyvalue = "";
        foreach ($result as $key => $value) {
            $keyvalue .= $key . $value["name"];
        }

        echo $keyvalue;
    }

    public function arrayAccessGet()
    {
        $this->TestIterator->clear();
        $this->TestIterator->add("honoka");
        $this->TestIterator->add("kotori");
        $this->TestIterator->add("umi");
        $result = $this->TestIterator->model1(3);

        // kotori
        echo $result[1]["name"];
    }

    public function arrayAccessSet()
    {
        $this->TestIterator->clear();
        $this->TestIterator->add("honoka");
        $this->TestIterator->add("kotori");
        $this->TestIterator->add("umi");
        $result = $this->TestIterator->model1(3);

        try {
            $result[1] = "nya-";
        } catch (\Exception $e) {
            echo get_class($e);
        }
    }
}
