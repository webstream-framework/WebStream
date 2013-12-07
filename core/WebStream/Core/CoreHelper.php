<?php
namespace WebStream\Core;

use WebStream\Module\Utility;
use WebStream\Module\Security;
use WebStream\Module\Container;
use WebStream\Module\Logger;
use WebStream\Exception\MethodNotFoundException;
use WebStream\Exception\IOException;

/**
 * CoreHelperクラス
 * @author Ryuichi TANAKA.
 * @since 2011/11/30
 * @version 0.4
 */
class CoreHelper
{
    use Utility;

    /** Viewオブジェクト */
    private $view;

    /**
     * コンストラクタ
     * @param object DIコンテナ
     */
    public function __construct(Container $container)
    {
        $this->view = $container->coreDelegator->getView();
    }

    /**
     * 初期処理
     * @param string ヘルパメソッド名
     * @param array<string> Viewパラメータ
     * @param array ヘルパ引数リスト
     */
    final public function __initialize($method, $params, $args)
    {
        // メソッド名を安全な値に置換
        $method = $this->snake2lcamel(Security::safetyOut($method));

        // 引数を安全な値に置換
        for ($i = 0; $i < count($args); $i++) {
            $args[$i] = Security::safetyOut($args[$i]);
        }

        // Helperメソッドを呼び出す
        if (method_exists($this, $method)) {
            $content = Security::safetyOut(call_user_func_array([$this, $method], $args));
            $cacheId = $this->getRandomstring(30);
            $temp = $this->getTemporaryDirectory() . "/" . $cacheId;
            $fileSize = file_put_contents($temp, $content, LOCK_EX);
            if ($fileSize === false) {
                throw new IOException("File write failure: " . $temp);
            } else {
                Logger::debug("Write temporary template file: " . $temp);
                Logger::debug("Compiled template file size: " . $fileSize);
            }

            $this->view->draw($temp, $params);
            unlink($temp);
        } else {
            $errorMsg = get_class($this) . "#" . $method . " is not defined.";
            throw new MethodNotFoundException($errorMsg);
        }
    }
}
