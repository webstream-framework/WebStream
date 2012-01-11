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
        '/action' => "test#test_action",
        '/core_controller' => "test#test_core_controller1",
        '/feed.:format' => "test#test_feed",
        '/csrf' => "test#test_csrf",
        '/csrf_post' => "test#test_csrf_post",
        '/snake' => "test_snake#index",
        '/snake_ng1' => "test__snake#index",
        '/snake_ng2' => "test_snake_#index",
        '/encoded/:name' => "test#test_encoded",
        '/similar/:name' => "test#test_similar1",
        '/similar/:name/:page' => "test#test_similar2"
    )
);
