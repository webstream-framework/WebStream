<?php
namespace WebStream\Annotation;

use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Exception\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * TemplateReader
 * @author Ryuichi TANAKA.
 * @since 2013/10/10
 * @version 0.4
 */
class TemplateReader extends AnnotationReader
{
    use Utility;

    /** template component */
    private $component;

    /**
     * @Override
     */
    public function readAnnotation($refClass, $method, $container)
    {
        $reader = new DoctrineAnnotationReader();
        $component = new TemplateComponent();

        $embedTemplates = [];
        $isAlreadyBaseRead = false;
        $coreDelegator = $container->coreDelegator;
        $templateDir = $this->camel2snake($coreDelegator->getPageName());

        try {
            $refMethod = $refClass->getMethod($method);
            if ($reader->getMethodAnnotation($refMethod, "\WebStream\Annotation\Inject")) {
                $annotations = $reader->getMethodAnnotations($refMethod);
                foreach ($annotations as $annotation) {
                    if ($annotation instanceof Template) {
                        $template = $annotation->getTemplate();
                        $name = $annotation->getName();
                        $type = "";
                        if ($name === $this->getModelVariableName() || $name === $this->getHelperVariableName()) {
                            $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'name'. ";
                            $errorMsg.= "'model' or 'helper' can not use name attribute in @Template.";
                            throw new AnnotationException($errorMsg);
                        }
                        if ($annotation->isBase()) {
                            if ($isAlreadyBaseRead) {
                                $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type'. ";
                                $errorMsg.= "Type of 'base' must be a only definition.";
                                throw new AnnotationException($errorMsg);
                            }
                            $component->setBase($templateDir . "/" . $template);
                            $isAlreadyBaseRead = true;
                            $type = "base";
                        } elseif ($annotation->isShared() && $name !== null) {
                            $embedTemplates[$name] = STREAM_VIEW_SHARED . "/" . $template;
                            $type = "shared";
                        } elseif ($annotation->isParts() && $name !== null) {
                            $embedTemplates[$name] = $templateDir . "/" . $template;
                            $type = "parts";
                        } else {
                            $errorMsg = "Argument of @Template('" . $template . "') annotation is not enough. ";
                            $errorMsg.= "Please check attribute 'name' or 'type'.";
                            throw new AnnotationException($errorMsg);
                        }
                        // Template#onInjectに書くと、FilterReader実行時に余計なログが出るためここに記述。
                        // これはDoctrineAnnotationのDocParser#Annotaionの制限で、特定のアノテーションを読み込まないように
                        // することができないため。L683付近の$instance = new $name();を書き換えれえば対応は可能。
                        Logger::debug("Template injected: name=" . $name . ",value=" . $template . ",type=" . $type);
                    }
                }
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        }

        $component->setEmbed($embedTemplates);
        $this->component = $component;
    }

    /**
     * テンプレートコンポーネントを返却する
     * @return object テンプレートコンポーネント
     */
    public function getComponent()
    {
        return $this->component;
    }
}
