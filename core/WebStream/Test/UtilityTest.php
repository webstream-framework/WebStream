<?php
namespace WebStream\Test;

use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Test\DataProvider\UtilityProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/UtilityProvider.php';

/**
 * UtilityTest
 * @author Ryuichi TANAKA.
 * @since 2012/01/15
 * @version 0.4
 */
class UtilityTest extends TestBase
{
    use Utility, UtilityProvider, TestConstant;

    public function setUp()
    {
        parent::setUp();
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
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

    /**
     * 正常系
     * ファイル検索できること
     * @test
     * @dataProvider fileSearchIteratorProvider
     */
    public function okFileSearch($path)
    {
        $classpath = $this->getRoot() . $path;
        $iterator = $this->getFileSearchIterator($this->getRoot());
        $isOk = false;
        foreach ($iterator as $filepath => $fileObject) {
            if ($filepath === $classpath) {
                $isOk = true;
            }
        }

        $this->assertTrue($isOk);
    }

    /**
     * 正常系
     * ファイルから名前空間を取得できること
     * @test
     * @dataProvider readNamespaceProvider
     */
    public function okReadNamespace($filepath, $namespace)
    {
        $path = $this->getNamespace($this->getProjectRootPath() . $filepath);
        $this->assertEquals($path, $namespace);
    }

    /**
     * 正常系
     * 名前空間がないファイルの場合、名前空間が取得できないこと
     * @test
     * @dataProvider readNoNamespaceProvider
     */
    public function okReadNoNamespace($filepath)
    {
        $path = $this->getNamespace($this->getProjectRootPath() . $filepath);
        $this->assertNull($path);
    }
}
