<?php
namespace WebStream\Test;
use WebStream\Utility;
use WebStream\Validator;
/**
 * Validatorクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2012/09/13
 */
require_once 'UnitTestBase.php';
 
class ValidatorTest extends UnitTestBase {
    private static $init = false;
    
    public function setUp() {
        parent::setUp();
        if (!self::$init) {
            define('STREAM_CLASSPATH', '\\WebStream\\');
            define('STREAM_APP_DIR', "core/test/testdata/app");
            self::$init = true;
        }
    }
    
    public function tearDown() {}
    
    /**
     * 正常系
     * バリデーションルールが妥当な場合、例外が発生しないこと
     * @dataProvider validRule
     */
    public function testOkValidation($validate_file) {
        \WebStream\import("/core/test/testdata/config/" . $validate_file);
        new Validator();
        $this->assertTrue(true);
    }
    
    /**
     * 異常系
     * 指定したコントローラが存在しない場合、例外が発生すること
     * @dataProvider invalidController
     * @expectedException WebStream\ClassNotFoundException
     */
    public function testNgInvalidController($validate_file) {
        \WebStream\import("/core/test/testdata/config/" . $validate_file);
        new Validator();
    }
    
    /**
     * 異常系
     * 指定したアクションが存在しない場合、例外が発生すること
     * @dataProvider invalidAction
     * @expectedException WebStream\MethodNotFoundException
     */
    public function testNgInvalidAction($validate_file) {
        \WebStream\import("/core/test/testdata/config/" . $validate_file);
        new Validator();
    }
    
    /**
     * 異常系
     * バリデーションルールの構文が間違っている場合、例外が発生すること
     * @dataProvider invalidRule
     * @expectedException WebStream\ValidatorException
     */
    public function testNgInvalidRule($validate_file) {
        \WebStream\import("/core/test/testdata/config/" . $validate_file);
        new Validator();
    }
    
    /**
     * 異常系
     * リクエストメソッドが間違っている場合、例外が発生すること
     * @expectedException WebStream\ValidatorException
     */
    public function testNgInvalidRequestMethod() {
        \WebStream\import("/core/test/testdata/config/validates.ng9");
        new Validator();
    }
    
    /**
     * 異常系
     * バリデーションルール「required」に指定されているGETパラメータが存在しない場合、422が返却されること
     */
    public function testNgNoRequiredParameterByGet() {
        $url = $this->root_url . "/get_validate1?dummy=111";
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "422");
    }

    /**
     * 異常系
     * バリデーションルール「required」に指定されているGETパラメータが存在するが値が空の場合、422が返却されること
     */
    public function testNgNoRequiredEmptyParameterByGet() {
        $url = $this->root_url . "/get_validate1?name1=";
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "422");
    }
    
    /**
     * 異常系
     * バリデーションルール「min_length[n]」に指定されているGETパラメータより小さい文字数が指定された場合、
     * 422が返却されること
     * @dataProvider lessThanMinLengthParameter
     */
    public function testNgLessThanMinLengthParameterByGet($str) {
        $url = $this->root_url . "/get_validate1?name1=aaa&name2=" . urlencode($str);
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "422");
    }

    /**
     * 異常系
     * バリデーションルール「max_length[n]」に指定されているGETパラメータより大きい文字数が指定された場合、
     * 422が返却されること
     * @dataProvider moreThanMaxLengthParameter
     */
    public function testNgMoreThanMaxLengthParameterByGet($str) {
        $url = $this->root_url . "/get_validate1?name1=aaa&name3=" . urlencode($str);
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "422");
    }
    
    /**
     * 異常系
     * バリデーションルール「min[n]」に指定されているGETパラメータより小さい値が指定された場合、
     * 422が返却されること
     * @dataProvider lessThanMinParameter
     */
    public function testNgLessThanMinParameterByGet($str) {
        $url = $this->root_url . "/get_validate1?name1=aaa&name4=" . $str;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "422");
    }
    
    /**
     * 異常系
     * バリデーションルール「max[n]」に指定されているGETパラメータより大きい値が指定された場合、
     * 422が返却されること
     * @dataProvider moreThanMaxParameter
     */
    public function testNgMoreThanMaxParameterByGet($str) {
        $url = $this->root_url . "/get_validate1?name1=aaa&name5=" . $str;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "422");
    }

    /**
     * 異常系
     * バリデーションルール「equal[s]」に指定されているGETパラメータと一致しない場合、422が返却されること
     */
    public function testNgNotEqualParameterByGet() {
        $url = $this->root_url . "/get_validate1?name1=aaa&name6=yui";
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "422");
    }
    
    /**
     * 異常系
     * バリデーションルール「length[n]」に指定されているGETパラメータと一致しない場合、422が返却されること
     * @dataProvider notEqualLengthParameter
     */
    public function testNgNotEqualLengthParameterByGet($str) {
        $url = $this->root_url . "/get_validate1?name1=aaa&name7=" . $str;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "422");
    }
    
    /**
     * 異常系
     * バリデーションルール「range[n..m]」に指定されているGETパラメータの範囲内でない場合、422が返却されること
     * @dataProvider outOfRangeParameter
     */
    public function testNgOutOfRangeParameterByGet($str) {
        $url = $this->root_url . "/get_validate1?name1=aaa&name8=" . $str;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "422");
    }

    /**
     * 異常系
     * バリデーションルール「regexp[/s/]」にマッチしないGETパラメータが指定された場合、422が返却されること
     */
    public function testNgUnmatchRegexpParameterByGet() {
        $url = $this->root_url . "/get_validate1?name1=aaa&name9=10";
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "422");
    }

    /**
     * 異常系
     * バリデーションルール「required」に指定されているPOSTパラメータが存在しない場合、422が返却されること
     */
    public function testNgNoRequiredParameterByPost() {
        $http = new \WebStream\HttpAgent();
        $params = array("dummy" => "111");
        $http->post($this->root_url . "/get_validate1", $params);
        $status_code = $http->getStatusCode();
        $this->assertEquals($status_code, "422");
    }

    /**
     * 異常系
     * バリデーションルール「required」に指定されているPOSTパラメータが存在するが値が空の場合、422が返却されること
     */
    public function testNgNoRequiredEmptyParameterByPost() {
        $http = new \WebStream\HttpAgent();
        $params = array("name1" => "");
        $http->post($this->root_url . "/get_validate1", $params);
        $status_code = $http->getStatusCode();
        $this->assertEquals($status_code, "422");
    }
    
    /**
     * 異常系
     * バリデーションルール「min_length[n]」に指定されているPOSTパラメータより小さい文字数が指定された場合、
     * 422が返却されること
     * @dataProvider lessThanMinLengthParameter
     */
    public function testNgLessThanMinLengthParameterByPost($str) {
        $http = new \WebStream\HttpAgent();
        $params = array("name1" => "aaa", "name2" => $str);
        $http->post($this->root_url . "/get_validate1", $params);
        $status_code = $http->getStatusCode();
        $this->assertEquals($status_code, "422");
    }

    /**
     * 異常系
     * バリデーションルール「max_length[n]」に指定されているPOSTパラメータより大きい文字数が指定された場合、
     * 422が返却されること
     * @dataProvider moreThanMaxLengthParameter
     */
    public function testNgMoreThanMaxLengthParameterByPost($str) {
        $http = new \WebStream\HttpAgent();
        $params = array("name1" => "aaa", "name3" => $str);
        $http->post($this->root_url . "/get_validate1", $params);
        $status_code = $http->getStatusCode();
        $this->assertEquals($status_code, "422");
    }

    /**
     * 異常系
     * バリデーションルール「min[n]」に指定されているPOSTパラメータより小さい値が指定された場合、
     * 422が返却されること
     * @dataProvider lessThanMinParameter
     */
    public function testNgLessThanMinParameterByPost($str) {
        $http = new \WebStream\HttpAgent();
        $params = array("name1" => "aaa", "name4" => $str);
        $http->post($this->root_url . "/get_validate1", $params);
        $status_code = $http->getStatusCode();
        $this->assertEquals($status_code, "422");
    }
    
    /**
     * 異常系
     * バリデーションルール「max[n]」に指定されているPOSTパラメータより大きい値が指定された場合、
     * 422が返却されること
     * @dataProvider moreThanMaxParameter
     */
    public function testNgMoreThanMaxParameterByPost($str) {
        $http = new \WebStream\HttpAgent();
        $params = array("name1" => "aaa", "name5" => $str);
        $http->post($this->root_url . "/get_validate1", $params);
        $status_code = $http->getStatusCode();
        $this->assertEquals($status_code, "422");
    }
    
    /**
     * 異常系
     * バリデーションルール「equal[s]」に指定されているPOSTパラメータと一致しない場合、422が返却されること
     */
    public function testNgNotEqualParameterByPost() {
        $http = new \WebStream\HttpAgent();
        $params = array("name1" => "aaa", "name6" => "yui");
        $http->post($this->root_url . "/get_validate1", $params);
        $status_code = $http->getStatusCode();
        $this->assertEquals($status_code, "422");
    }

    /**
     * 異常系
     * バリデーションルール「length[n]」に指定されているPOSTパラメータと一致しない場合、422が返却されること
     * @dataProvider notEqualLengthParameter
     */
    public function testNgNotEqualLengthParameterByPost($str) {
        $http = new \WebStream\HttpAgent();
        $params = array("name1" => "aaa", "name7" => $str);
        $http->post($this->root_url . "/get_validate1", $params);
        $status_code = $http->getStatusCode();
        $this->assertEquals($status_code, "422");
    }

    /**
     * 異常系
     * バリデーションルール「range[n..m]」に指定されているPOSTパラメータの範囲内でない場合、422が返却されること
     * @dataProvider outOfRangeParameter
     */
    public function testNgOutOfRangeParameterByPost($str) {
        $http = new \WebStream\HttpAgent();
        $params = array("name1" => "aaa", "name8" => $str);
        $http->post($this->root_url . "/get_validate1", $params);
        $status_code = $http->getStatusCode();
        $this->assertEquals($status_code, "422");
    }

    /**
     * 異常系
     * バリデーションルール「regexp[/s/]」にマッチしないPOSTパラメータが指定された場合、422が返却されること
     */
    public function testNgUnmatchRegexpParameterByPost() {
        $http = new \WebStream\HttpAgent();
        $params = array("name1" => "aaa", "name9" => "10");
        $http->post($this->root_url . "/get_validate1", $params);
        $status_code = $http->getStatusCode();
        $this->assertEquals($status_code, "422");
    }
}