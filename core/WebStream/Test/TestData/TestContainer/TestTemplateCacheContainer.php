<?php
namespace WebStream\Test\TestData\TestContainer;

class TestTemplateCacheContainer
{
    private $action;

    public function __construct($data)
    {
        $this->action = $data["action"];
    }

    public function action()
    {
        return $this->action;
    }
}
