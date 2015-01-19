<?php
namespace WebStream\Test\TestData\Sample\App\Service;

use WebStream\Core\CoreService;

class TestHelperService extends CoreService
{
    private $name;
    private $map;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setMap(array $map)
    {
        $this->map = $map;
    }

    public function getMap()
    {
        return $this->map;
    }
}
