<?php
/**
 * ルーティングルールを記述する
 */
Router::setRule(
    array(
        '/index' => "sample#index",
        '/yuruyuri' => "yuru_yuri#execute"
    )
);
