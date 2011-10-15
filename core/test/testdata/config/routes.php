<?php
/**
 * ルーティングルールを記述する
 */
Router::setRule(
    array(
        '/' => "test#test1",
        '/top' => "test#test2",
        '/top/:id' => "test#test3",
        '/notfound/controller' => "test#notfound",
        '/notfound/action' => "test#notfound",
        '/render' => "test#render",
        '/layout' => "test#layout",
        '/redirect' => "test#redirect",
        '/load' => "test#load",
        '/before' => "test#before",
        '/after' => "test#after",
        '/error2' => "Test#test1",
        '/error3' => "teSt#test1",
        '/action' => "test#testAction",
        '/core_controller' => "test#testCoreController1",
        '/feed.:format' => "test#testFeed",
        '/csrf' => "test#testCsrf",
        '/csrf_post' => "test#testCsrfPost",
        '/snake' => "test_snake#index",
        '/snake_ng1' => "test__snake#index",
        '/snake_ng2' => "test_snake_#index",
        '/encoded/:name' => "test#testEncoded"
    )
);
