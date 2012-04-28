<?php
/**
 * ルーティングルールを記述する
 */
Router::setRule(
    array(
        '/index' => "sample#index",
        '/index_helper' => "sample#helper",
        '/yuruyuri' => "yuru_yuri#execute"
    )
);
