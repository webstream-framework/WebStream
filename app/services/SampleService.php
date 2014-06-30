<?php
namespace WebStream\Sample;

use WebStream\Core\CoreService;

class SampleService extends CoreService
{
    private $title;

    private $data;

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getLibraryData()
    {
        $lib = new SampleLibrary();

        return $lib->getName();
    }

    public function setDescription()
    {
        $this->data = $this->Sample->getData();
    }

    public function getDescription()
    {
        return $this->data;
    }
}
