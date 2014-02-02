<?php
namespace WebStream\Annotation;

use WebStream\Module\Utility;
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
                        if ($annotation->isBase()) {
                            if ($isAlreadyBaseRead) {
                                $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type'. ";
                                $errorMsg.= "Type of 'base' must be a only definition.";
                                throw new AnnotationException($errorMsg);
                            }
                            $component->setBase($templateDir . "/" . $template);
                            $isAlreadyBaseRead = true;
                        } elseif ($annotation->isShared() && $name !== null) {
                            $embedTemplates[$name] = STREAM_VIEW_SHARED . "/" . $template;
                        } elseif ($annotation->isParts() && $name !== null) {
                            $embedTemplates[$name] = $templateDir . "/" . $template;
                        } else {
                            $errorMsg = "Argument of @Template('" . $template . "') annotation is not enough. ";
                            $errorMsg.= "Please check attribute 'name' or 'type'.";
                            throw new AnnotationException($errorMsg);
                        }
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
