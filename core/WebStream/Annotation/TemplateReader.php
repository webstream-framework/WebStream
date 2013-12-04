<?php
namespace WebStream\Annotation;

use WebStream\Module\Utility;
use WebStream\Exception\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;

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
    public function readAnnotation($refClass, $methodName, $container)
    {
        $reader = new DoctrineAnnotationReader();
        $component = new TemplateComponent();

        $embedTemplates = [];
        $isAlreadyBaseRead = false;
        $coreDelegator = $container->coreDelegator;
        $templateDir = $this->camel2snake($coreDelegator->getPageName());

        while ($refClass !== false) {
            $methods = $refClass->getMethods();
            foreach ($methods as $method) {
                if ($refClass->getName() !== $method->class || $methodName !== $method->name) {
                    continue;
                }
                $annotations = $reader->getMethodAnnotations($method);

                $isInject = false;
                foreach ($annotations as $annotation) {
                    if ($annotation instanceof Inject) {
                        $isInject = true;
                    }
                }

                if ($isInject) {
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
            }

            $refClass = $refClass->getParentClass();
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
