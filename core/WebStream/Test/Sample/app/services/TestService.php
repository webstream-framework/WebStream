<?php
namespace WebStream\Test\TestData\Sample\App\Service;

use WebStream\Core\CoreService;
use WebStream\Test\TestData\Sample\App\Library\SampleLibrary;

class TestService extends CoreService
{
    public function service1()
    {
        return "Music S.T.A.R.T!!";
    }

    public function service2()
    {
        $sampleLibrary = new SampleLibrary();

        return $sampleLibrary->getName();
    }
}
