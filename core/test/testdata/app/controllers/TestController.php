<?php
class TestController extends CoreController {
    /**
     * ルーティングパスのテスト
     * path: /
     */
    public function test1() {
        echo "test1";
    }
    
    /**
     * ルーティングパスのテスト
     * path: /top
     */
    public function test2() {
        echo "test2";
    }
    
    /**
     * ルーティングパスのテスト
     * path: /top/:id
     */
    public function test3($params) {
        echo $params["id"];
    }
    
    /**
     * ルーティングパスのテスト
     * path: /action
     */
    public function testAction() {
        echo "testAction";
    }
    
    public function testFeed($params) {
        echo $params["format"];
    }
    
    /**
     * CoreControllerのテスト
     */
    public function testCoreController1() {
        echo $this->Test instanceof CoreService;
    }
    
    /**
     * Csrfのテスト
     */
    public function testCsrf() {
        $this->render("test");
    }
    
    /**
     * CsrfのPOSTテスト
     */
    public function testCsrfPost() {
        var_dump(session_id());
        echo "csrf post is ok.";
    }
    
    /**
     * URLエンコード文字列テスト
     */
    public function testEncoded($params) {
        echo $params["name"];
    }
    
    /**
     * 定義名が部分一致する場合のテスト1
     */
    public function testSimilar1() {
        echo "similar1";
    }

    /**
     * 定義名が部分一致する場合のテスト2
     */
    public function testSimilar2($params) {
        echo "similar". $params["page"];
    }
}
