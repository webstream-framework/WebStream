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
        '/action2' => "test#test_action_hoge_fuga",
        '/core_controller' => "test#test_core_controller1",
        '/feed.:format' => "test#test_feed",
        '/csrf' => "test#test_csrf",
        '/csrf_post' => "test#test_csrf_post",
        '/snake' => "test_snake#index",
        '/snake2' => "test_snake_hoge_fuga#index",
        '/snake_ng1' => "test__snake#index",
        '/snake_ng2' => "test_snake_#index",
        '/encoded/:name' => "test#test_encoded",
        '/similar/:name' => "test#test_similar1",
        '/similar/:name/:page' => "test#test_similar2",
        '/no_service' => "test_no_service_class#execute",
        '/no_service2' => "test_no_service_method#execute",
        '/no_service_no_model' => "test_no_service_and_model#execute",
        '/exist_service_exist_model_no_method' => "test_exist_service_exist_model_no_method#execute",
        '/no_exist_service_exist_model_no_method' => "test_exist_service_no_model_no_method#execute",
        '/no_service_exist_model_no_method' => "test_no_service_exist_model_no_method#execute",
        '/exist_service_no_model_no_method' => "test_exist_service_no_model_no_method#execute",
        '/exist_service_exist_model_exist_model_method_param' => "test_exist_service_exist_model_exist_model_method#send_param",
        '/exist_service_exist_model_exist_model_method_params' => "test_exist_service_exist_model_exist_model_method#send_params",
        '/view1' => "test_view#index",
        '/view2' => "test_view#sub_index",
        '/view3' => "test_aaa_bbb_view#index",
        '/view4' => "test_aaa_bbb_view#sub_index",
        '/layout1' => "test_layout_view#index",
        '/layout2' => "test_layout_view#sub_index"
    )
);
