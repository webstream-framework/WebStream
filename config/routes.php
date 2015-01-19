<?php
namespace WebStream\Delegate;

/**
 * ルーティングルールを記述する
 */
Router::setRule([
    '/index' => "sample#index",
    '/model1' => "sample#model1"
]);
