<?php
require 'core/AutoImport.php';
// クラスをオートロード
function __autoload($class_name) {
    import("core/" . $class_name);
}
// ルーティングルールをロード
import("config/routes");
// 関数群をロード
import("core/Functions");
// 独自例外をロード
import("core/Exception");
// Loggerを初期化
Logger::init();
// アプリケーションを起動する
$app = new Application();
$app->run();