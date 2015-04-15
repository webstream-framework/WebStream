<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Template;

class TestTwigTemplateController extends CoreController
{
    /**
     * @Inject
     * @Template("index1.twig", engine="twig")
     */
    public function index1()
    {
        // twigテンプレートを使用
    }

    /**
     * @Inject
     * @Template("index2.twig", engine="twig")
     */
    public function index2()
    {
        // modelの内容を描画できること
    }

    /**
     * @Inject
     * @Template("index3.twig", engine="twig")
     */
    public function index3()
    {
        // DBの値を描画できること
    }

    /**
     * @Inject
     * @Template("index4.twig", engine="twig")
     */
    public function index4()
    {
        // sharedテンプレートを使用できること
    }

    /**
     * @Inject
     * @Template("index5.twig", engine="twig")
     */
    public function index5()
    {
        // sharedに同名のファイルがある場合、pageName配下ディレクトリが優先されること
    }

    /**
     * @Inject
     * @Template("index6.twig", engine="twig")
     */
    public function index6()
    {
        // partsテンプレートを使用できること
    }

    /**
     * @Inject
     * @Template("undefined.twig", engine="twig")
     */
    public function error1()
    {
        // 存在しないテンプレート
    }
}
