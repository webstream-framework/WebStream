<?php
namespace WebStream;
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
        '/filter' => "test_filter_annotation#execute",
        '/error2' => "Test#test1",
        '/error3' => "teSt#test1",
        '/action' => "test#test_action",
        '/action2' => "test#test_action_hoge_fuga",
        '/core_controller' => "test#test_core_controller1",
        '/feed.:format' => "test#test_feed",
        '/csrf' => "test#test_csrf",
        '/csrf_get_view' => "test#test_csrf_get_view",
        '/csrf_get' => "test#test_csrf_get",
        '/csrf_post_view' => "test#test_csrf_post_view",
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
        '/view5' => "test_view#index2",
        '/layout1' => "test_layout_view#index",
        '/layout2' => "test_layout_view#sub_index",
        '/resource/html' => "test_resource#html",
        '/resource/rss' => "test_resource#rss",
        '/resource/xml' => "test_resource#xml",
        '/resource/rdf' => "test_resource#rdf",
        '/resource/atom' => "test_resource#atom",
        '/notfound_render' => "test#test_not_found_render",
        '/get_request' => "test_request#get",
        '/post_request' => "test_request#post",
        '/post_receiver' => "test_request#post_receiver",
        '/prohibit_override' => "test_prohibit_override#render",
        '/set_session' => "test_session#set",
        '/get_session' => "test_session#get",
        '/status301' => "test#test301",
        '/status400' => "test#test400",
        '/status403' => "test#test403",
        '/status404' => "test#test404",
        '/status500' => "test#test500",
        '/status_unknown' => "test#test_unknown_status_code",
        '/helper1' => "test#test_helper_html1",
        '/helper2' => "test#test_helper_html2",
        '/helper3' => "test#test_helper_string",
        '/helper4' => "test#test_helper_snake",
        '/helper5' => "test#test_helper_camel",
        '/helper6' => "test#test_helper_notfound_method",
        '/attr' => "test#test_attribute_value",
        '/json' => "test#test_json",
        '/jsonp' => "test#test_jsonp",
        '/validate1' => "test_validate#validate1",
        '/get_validate1' => "test_validate#get_param_validate1",
        '/validate_handling' => "test_validate_error_handling#validate1",
        '/validate_handling2' => "test_validate_error_handling2#validate1",
        '/basic_auth' => "test_basic_auth#execute",
        '/basic_auth2' => "test_basic_auth#execute2",
        '/filter_multi' => "test_filter_multi#execute",
        '/cache' => "test_cache_annotation#execute",
        '/response_cache' => "test_response_cache#execute",
        '/handled_csrf_view' => "test_handled_csrf#show_view",
        '/handled_csrf' => "test_handled_csrf#execute"
    )
);
