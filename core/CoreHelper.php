<?php
namespace WebStream;
/**
 * CoreHelperクラス
 * @author Ryuichi TANAKA.
 * @since 2012/04/28
 */
class CoreHelper extends CoreBase {
    /** テンプレートリスト */
    private $templates;
    /** レンダリングメソッドリスト */
    private $renderMethods;
    /** レンダリングパラメータ */
    private $params;

    /**
     * コンストラクタ
     * @param String ページ名
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * View呼び出し時に使用する変数をセット
     * @param Hash テンプレートリスト
     * @param Hash レンダリングメソッドリスト
     * @param Hash レンダリングパラメータ
     */
    final public function __setViewParams($templates, $renderMethods, $params) {
        $this->templates = $templates;
        $this->renderMethods = $renderMethods;
        $this->params = $params;
    }

    /**
     * メソッド呼び出しの初期化処理を実行する
     * @param String メソッド名
     * @param Array メソッドの引数
     */
    final public function __initialize($method, $args) {
        // メソッド名を安全な値に置換
        $methodName = Utility::snake2lcamel(safetyOut($method));
        // 引数を安全な値に置換
        for ($i = 0; $i < count($args); $i++) {
            $args[$i] = safetyOut($args[$i]);
        }
        // Helperメソッドを呼び出す
        if (method_exists($this, $methodName)) {
            $content = call_user_func_array(array($this, $methodName), $args);
            echo $this->__callTemplate($content);
        }
        else {
            $className = $this->__toString();
            throw new MethodNotFoundException("${className}#${methodName} is not defined.");
        }
    }

    /**
     * Helperの中でテンプレートを呼び出す
     * @param String 置換対象の文字列
     * @return String 置換後のテンプレートファイルの内容
     */
    final private function __callTemplate($s) {
        return preg_replace_callback('/@\{(.*?)\}/', array($this, '__callTemplateCallback'), $s);
    }

    /**
     * テンプレート呼び出しを実行する
     * @param Hash テンプレート構文のマッチング結果
     * @return String 置換後のテンプレートファイルの内容
     */
    final private function __callTemplateCallback($matches) {
        $view = $this->__getView();
        $template = $this->templates[$matches[1]];
        $method = $this->renderMethods[$template];
        return $view->{$method}($template, $this->params);
    }
}
