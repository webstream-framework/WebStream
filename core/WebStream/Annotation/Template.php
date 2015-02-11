<?php
namespace WebStream\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IMethod;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Logger;
use WebStream\Module\Container;
use WebStream\Module\Utility;

/**
 * Template
 * @author Ryuichi TANAKA.
 * @since 2013/10/10
 * @version 0.4.1
 *
 * @Annotation
 * @Target("METHOD")
 */
class Template extends Annotation implements IMethod, IRead
{
    use Utility;

    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotaion;

    /**
     * @var AnnotationContainer 注入結果
     */
    private $injectedContainer;

    /**
     * {@inheritdoc}
     */
    public function onInject(AnnotationContainer $annotation)
    {
        $this->annotation = $annotation;
        $this->injectedContainer = new AnnotationContainer();
        Logger::debug("@Template injected.");
    }

    /**
     * {@inheritdoc}
     */
    public function onInjected()
    {
        return $this->injectedContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(CoreInterface &$instance, Container $container, \ReflectionMethod $method)
    {
        $template = $this->annotation->value; // 属性値なしの第一引数はテンプレート名
        $type = $this->annotation->type;
        $name = $this->annotation->name;
        $partsList = [];

        if (PHP_OS === "WIN32" || PHP_OS === "WINNT") {
            if (preg_match("/^.*[. ]|.*[\p{Cntrl}\/:*?\"<>|].*|(?i:CON|PRN|AUX|CLOCK\$|NUL|COM[1-9]|LPT[1-9])(?:[.].+)?$/", $template)) {
                throw new AnnotationException("Invalid string contains in @Template('" . safetyOut($template) . "'");
            }
        } else {
            if (preg_match("/:|\//", $template)) {
                throw new AnnotationException("Invalid string contains in @Template('" . safetyOut($template) . "'");
            }
        }

        // type属性は複数指定可能
        $typeList = $type ?: ["base"];
        if (!is_array($typeList)) {
            $typeList = [$typeList];
        }

        // name属性は複数指定されたらエラーとする
        // 複数定義された場合は同じ値が異なる変数に代入されるだけ
        // 本来は1つしか指定されないはずだが、許可する以上はリストとして処理
        // テンプレートで変数化するときにsharedとpartsの変数名が重複しないようにprefix(parts|shared)をつける
        // さらに、{"parts","shared"}と{"shared","parts"}は同じものと判定する
        if ($name !== null) {
            if (is_array($name)) {
                $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'name'. ";
                $errorMsg = "The name attribute can not be specified multiple in @Template.";
                throw new AnnotationException($errorMsg);
            }

            // 不正なnameが指定された場合エラー
            if (!preg_match("/^[a-zA-Z_][a-z-A-Z0-9_]+$/", $name)) {
                $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'name': " . safetyOut($name);
                throw new AnnotationException($errorMsg);
            }

            // model、helperオブジェクトを格納する用の変数は予約されているので重複指定はエラー
            if ($name === $this->getModelVariableName() || $name === $this->getHelperVariableName()) {
                $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'name'. ";
                $errorMsg.= "'" . $this->getModelVariableName() . "' or '" . $this->getHelperVariableName() . "' can not use name attribute in @Template.";
                throw new AnnotationException($errorMsg);
            }
        }

        // ページ名からディレクトリ名を取得
        $templateDir = $this->camel2snake($container->coreDelegator->getPageName());
        $this->injectedContainer->name = $templateDir . "/" . $template;

        // type="base"が設定された場合は後勝ちでベーステンプレートとする
        // name属性は指定されても無視
        if (in_array("base", $typeList)) {
            if (in_array("shared", $typeList)) { // type={"base","shared"}
                $this->injectedContainer->base = STREAM_VIEW_SHARED . "/" . $template;
            } elseif (in_array("parts", $typeList)) { // type={"base","parts"}はありえない
                $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type'.";
                $errorMsg.= "The type attribute can't setting 'base' and 'type'.";
                throw new AnnotationException($errorMsg);
            }
        } else {
            // type={"shared","parts"}
            if (count($typeList) === 2 && in_array("shared", $typeList) && in_array("parts", $typeList)) {
                // name属性が必須
                if ($name === null) {
                    $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type'.";
                    $errorMsg.= "The name attribute is required if type attribute is 'shared' or 'parts'.";
                    throw new AnnotationException($errorMsg);
                }
                $partsList[$name] = STREAM_VIEW_SHARED . "/" . $template;
            } elseif (count($typeList) === 1) {
                // type="shared"
                if (in_array("shared", $typeList)) {
                    // name属性が必須
                    if ($name === null) {
                        $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type'.";
                        $errorMsg.= "The name attribute is required if type attribute is 'shared'.";
                        throw new AnnotationException($errorMsg);
                    }
                    $partsList[$name] = STREAM_VIEW_SHARED . "/" . $template;
                // type="parts"
                } elseif (in_array("parts", $typeList)) {
                    // name属性が必須
                    if ($name === null) {
                        $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type'.";
                        $errorMsg.= "The name attribute is required if type attribute is 'parts'.";
                        throw new AnnotationException($errorMsg);
                    }
                    $partsList[$name] = $templateDir . "/" . $template;
                } else {
                    $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type' included " . $typeList[0] . ".";
                    throw new AnnotationException($errorMsg);
                }
            } else {
                $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type'.";
                throw new AnnotationException($errorMsg);
            }
        }

        $this->injectedContainer->parts = $partsList;
    }
}
