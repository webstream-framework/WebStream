<?php
namespace WebStream;
require 'core/AutoImport.php';
require 'core/Functions.php';
// クラスをオートロード
function __autoload($class_name) {
    import("core/" . $class_name);
}
// シャットダウン時のハンドリング関数を登録
register_shutdown_function('WebStream\shutdownHandler');
// コアクラスをインポート
importAll("core");
// ルーティングルールをロード
import("config/routes");
// Loggerを初期化
Logger::init();
// アプリケーションを起動する
$app = new Application();
$app->run();