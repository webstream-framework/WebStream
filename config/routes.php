<?php
namespace WebStream;
/**
 * ルーティングルールを記述する
 */
Router::setRule(
    array(
        '/index' => "sample#index",
        '/model1' => "sample#model1",
        '/model2' => "sample#model2",
        '/model3' => "sample#model3",
        '/render' => "sample#anno_render",
        '/index_helper' => "sample#helper",
        '/yuruyuri' => "yuru_yuri#execute",
        '/validate' => "sample#validate",
        '/validate_form' => "sample#validate_form",
        '/basic_auth' => "sample#basic_auth",
        '/response_cache' => "sample#response_cache"
    )
);
