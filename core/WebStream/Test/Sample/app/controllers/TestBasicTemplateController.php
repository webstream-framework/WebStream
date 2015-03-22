<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Header;
use WebStream\Annotation\Template;

class TestBasicTemplateController extends CoreController
{
    /**
     * @Inject
     * @Template("index1.tmpl")
     */
    public function index1()
    {
        // engine属性なしの場合、Basic
    }

    /**
     * @Inject
     * @Template("index2.tmpl", engine="basic")
     */
    public function index2()
    {
        // 明示的にengine属性指定
    }

    /**
     * @Inject
     * @Template("index3.tmpl")
     */
    public function index3()
    {
        // partsが読めること
    }

    /**
     * @Inject
     * @Template("shared1.tmpl")
     */
    public function index4()
    {
        // sharedが読めること
    }

    /**
     * @Inject
     * @Template("shared_index5.tmpl")
     */
    public function index5()
    {
        // sharedに同名のファイルがある場合、pageName配下ディレクトリが優先されること
    }

    /**
     * @Inject
     * @Template("index6.tmpl")
     */
    public function index6()
    {
        // modelアクセスできること
    }

    /**
     * @Inject
     * @Template("index7.tmpl")
     */
    public function index7()
    {
        // helperアクセスできること
    }

    /**
     * @Inject
     * @Template("index8.tmpl")
     */
    public function index8()
    {
        // partsの中でmodelアクセスできること
    }

    /**
     * @Inject
     * @Template("index9.tmpl")
     */
    public function index9()
    {
        // partsの中でhelperアクセスできること
    }

    /**
     * @Inject
     * @Template("sub/index10.tmpl")
     */
    public function index10()
    {
        // ディレクトリを含めたテンプレートパスにアクセスできること
    }

    /**
     * @Inject
     * @Template("sub/index11.tmpl")
     */
    public function index11()
    {
        // sharedディレクトリを含めたテンプレートパスにアクセスできること
    }

    /**
     * @Inject
     * @Template("index12.tmpl", cacheTime=10)
     */
    public function index12()
    {
        // cacheTimeに数値を指定
    }

    /**
     * @Inject
     * @Template("index1.tmpl", engine="unknown")
     */
    public function error1()
    {
        // 不明なengineを指定した場合
    }

    /**
     * @Inject
     * @Template("error2.tmpl")
     */
    public function error2()
    {
        // partsが存在しない場合
    }

    /**
     * @Inject
     * @Template("error3.tmpl")
     */
    public function error3()
    {
        // 存在しないmodelメソッドにアクセスした場合
    }

    /**
     * @Inject
     * @Template("error4.tmpl")
     */
    public function error4()
    {
        // 存在しないhelperメソッドにアクセスした場合
    }

    /**
     * @Inject
     * @Template("error5.tmpl", cacheTime="hoge")
     */
    public function error5()
    {
        // cacheTimeに数値以外の値を指定した場合
    }

    /**
     * @Inject
     * @Template("error6.tmpl", cacheTime=-1)
     */
    public function error6()
    {
        // cacheTimeに負数を指定した場合
    }

    /**
     * @Inject
     * @Template("undefined.tmpl",)
     */
    public function errror7()
    {
        // 存在しないテンプレートを指定
    }

    /**
     * @Inject
     * @Template("../shared/shared1.tmpl",)
     */
    public function error8()
    {
        // ディレクトリをたどるパス指定をした場合
    }

    /**
     * @Inject
     * @Header(contentType="xml")
     * @Template("xml.tmpl")
     */
    public function xml()
    {
        // テンプレートでxmlが出力できること
    }

    /**
     * @Inject
     * @Template("javascript1.tmpl")
     */
    public function javascript1()
    {
        // テンプレートでJavaScriptが出力できること
        // 改行コード(\n)を含む文字列のalert
    }

    /**
     * @Inject
     * @Template("javascript2.tmpl")
     */
    public function javascript2()
    {
        // テンプレートでJavaScriptが出力できること
        // 改行コード(\r)を含む文字列のalert
    }

    /**
     * @Inject
     * @Template("javascript3.tmpl")
     */
    public function javascript3()
    {
        // テンプレートでJavaScriptが出力できること
        // 改行コード(\r\n)を含む文字列のalert
    }

    /**
     * @Inject
     * @Template("javascript4.tmpl")
     */
    public function javascript4()
    {
        // テンプレートでJavaScriptが出力できること
        // ユニコードエスケープ文字列を実行
    }

    /**
     * @Inject
     * @Template("javascript5.tmpl")
     */
    public function javascript5()
    {
        // テンプレートでJavaScriptが出力できること
        // シングルクオートのエスケープ
    }

    /**
     * @Inject
     * @Template("javascript6.tmpl")
     */
    public function javascript6()
    {
        // テンプレートでJavaScriptが出力できること
        // ダブルクオートのエスケープ
    }

    /**
     * @Inject
     * @Template("javascript7.tmpl")
     */
    public function javascript7()
    {
        // テンプレートでJavaScriptが出力できること
        // scriptタグのエスケープ
    }

    /**
     * @Inject
     * @Template("javascript8.tmpl")
     */
    public function javascript8()
    {
        // テンプレートでJavaScriptが出力できること
        // 改行コード以外のコードのエスケープ
    }

    /**
     * @Inject
     * @Template("html1.tmpl")
     */
    public function html1()
    {
        // エスケープしないでHTML出力
    }

    /**
     * @Inject
     * @Template("html2.tmpl")
     */
    public function html2()
    {
        // エスケープしてHTML出力
    }
}
