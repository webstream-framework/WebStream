<?php
namespace WebStream\Test;

use WebStream\Module\Utility;
use WebStream\Test\TestConstant;

require_once 'TestBase.php';
require_once 'TestConstant.php';

/**
 * UtilityTest
 * @author Ryuichi TANAKA.
 * @since 2012/01/15
 * @version 0.4
 */
class UtilityTest extends TestBase
{
    use Utility, TestConstant;

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * 正常系
     * プロジェクトルートパスが取得できること
     * @test
     */
    public function okGetProjectRoot()
    {
        $this->assertEquals($this->getProjectRootPath(), $this->getRoot());
    }
}
