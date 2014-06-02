<?php
namespace WebStream\Annotation\Reader;

use WebStream\Module\Utility;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Exception\Extend\AnnotationException;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * TemplateReader
 * @author Ryuichi TANAKA.
 * @since 2013/10/10
 * @version 0.4
 */
class TemplateReader extends AbstractAnnotationReader
{
    use Utility;

    /** テンプレートコンテナ */
    private $templateContainer;

    /**
     * {@inheritdoc}
     */
    public function onRead()
    {
        $this->annotation = $this->reader->getAnnotation("WebStream\Annotation\Template");
        $this->templateContainer = new AnnotationContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->annotation === null) {
            return;
        }

        $refClass = $this->reader->getReflectionClass();
        $action = $this->reader->getContainer()->router->action();

        $annotationContainerKey = $refClass->getName() . "#" . $action;
        if (!array_key_exists($annotationContainerKey, $this->annotation)) {
            return;
        }

        $actionContainerList = $this->annotation[$annotationContainerKey];
        $this->templateContainer->isBase = false;

        try {
            $partsList = [];
            foreach ($actionContainerList as $actionContainer) {
                // 属性値なしの第一引数はテンプレート名
                $template = $actionContainer->value;

                if (PHP_OS === "WIN32" || PHP_OS === "WINNT") {
                    if (preg_match("/^.*[. ]|.*[\p{Cntrl}\/:*?\"<>|].*|(?i:CON|PRN|AUX|CLOCK\$|NUL|COM[1-9]|LPT[1-9])(?:[.].+)?$/", $template)) {
                        throw new AnnotationException("Invalid string contains in @Template('" . safetyOut($template) . "'");
                    }
                } else {
                    if (preg_match("/:|\//", $template)) {
                        throw new AnnotationException("Invalid string contains in @Template('" . safetyOut($template) . "'");
                    }
                }

                // name属性は複数指定されたらエラーとする
                // 複数定義された場合は同じ値が異なる変数に代入されるだけ
                // 本来は1つしか指定されないはずだが、許可する以上はリストとして処理
                $name = $actionContainer->name;
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

                // type属性は複数指定可能
                $typeList = $actionContainer->type ?: [];
                if (!is_array($typeList)) {
                    $typeList = [$typeList];
                }
                // ページ名からディレクトリ名を取得
                $templateDir = $this->camel2snake($this->reader->getContainer()->coreDelegator->getPageName());
                // ベーステンプレートは暫定的に1番はじめに指定されたテンプレートを設定する
                if ($this->templateContainer->base === null) {
                    $this->templateContainer->base = $templateDir . "/" . $template;
                    $this->templateContainer->isBase = false;
                }

                // type="base"が設定された場合は後勝ちでベーステンプレートとする
                if (in_array("base", $typeList)) {
                    // type="base"が複数指定された場合、エラーとする
                    if ($this->templateContainer->isBase) {
                        $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type'. ";
                        $errorMsg.= "The type attribute 'base' must be a only definition.";
                        throw new AnnotationException($errorMsg);
                    }
                    // type={"base","shared"}
                    if (in_array("shared", $typeList)) { // baseかつsharedの場合はname属性の指定不要
                        $this->templateContainer->base = STREAM_VIEW_SHARED . "/" . $template;
                    } else { // type="parts"が含まれていた場合もbaseのみの場合と同様の扱い
                        $this->templateContainer->base = $templateDir . "/" . $template;
                    }
                    $this->templateContainer->isBase = true;
                } elseif (in_array("shared", $typeList)) {
                    // sharedのみの場合、name属性必須
                    if ($name === null) {
                        $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type'. ";
                        $errorMsg.= "The name attribute is required if type attribute is 'shared'.";
                        throw new AnnotationException($errorMsg);
                    }
                    // type={"shared","parts"}は矛盾するのでエラー
                    if (in_array("parts", $typeList)) {
                        $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type'. ";
                        $errorMsg.= "Can not be specified at the same time 'parts' and 'shared' in the type attribute.";
                        throw new AnnotationException($errorMsg);
                    }
                    $partsList[$name] = STREAM_VIEW_SHARED . "/" . $template;
                } elseif (in_array("parts", $typeList)) {
                    // partsのみの場合、name属性必須
                    if ($name === null) {
                        $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type'. ";
                        $errorMsg.= "The name attribute is required if type attribute is 'parts'.";
                        throw new AnnotationException($errorMsg);
                    }
                    // nameが重複した場合はエラーにはせず後勝ち
                    $partsList[$name] = $templateDir . "/" . $template;
                }

                $this->templateContainer->parts = $partsList;
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        }
    }

    /**
     * テンプレートコンテナを返却する
     * @return AnnotationContainer テンプレートコンテナ
     */
    public function getTemplateContainer()
    {
        return $this->templateContainer;
    }
}
