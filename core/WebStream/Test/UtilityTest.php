<?php
namespace WebStream\Test;

use WebStream\Module\Utility;
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
     * @dataProvider fileSearchProvider
     */
    public function okFileSearch($word, $classpath)
    {
        $list = $this->fileSearch($word);
        $this->assertEquals($classpath, $list[0]);
    }

    /**
     * 正常系
     * 複数ファイル検索できること
     * @test
     * @dataProvider multipleFileSearchProvider
     */
    public function okMultipleFileSearch($word, $classpath1, $classpath2)
    {
        $list = $this->fileSearch($word);
        $this->assertEquals($classpath1, $list[0]);
        $this->assertEquals($classpath2, $list[1]);
    }

    /**
     * 正常系
     * 正規表現でファイル検索できること
     * @test
     * @dataProvider regexpFileSearchProvider
     */
    public function okRegexpFileSearch($regexp, $classpath)
    {
        $list = $this->fileSearchRegexp($regexp);
        $this->assertEquals($classpath, $list[0]);
    }

    /**
     * 正常系
     * 複数ファイル検索できること
     * @test
     * @dataProvider regexpMultipleFileSearchProvider
     */
    public function okRegexpMultipleFileSearch($regexp, $classpath1, $classpath2)
    {
        $list = $this->fileSearchRegexp($regexp);
        $this->assertEquals($classpath1, $list[0]);
        $this->assertEquals($classpath2, $list[1]);
    }
}
