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
        '/index_helper' => "sample#helper",
        '/yuruyuri' => "yuru_yuri#execute",
        '/validate' => "sample#validate",
        '/validate_form' => "sample#validate_form"
    )
);
