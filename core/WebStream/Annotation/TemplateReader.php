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

    /** template directory */
    private $templateDir;

    /**
     * @Override
     */
    public function readAnnotation($refClass, $method)
    {
        $reader = new DoctrineAnnotationReader();

        $baseTemplate = null;
        $embedTemplates = [];
        $isAlreadyDefinitionBaseTemplate = false;

        try {
            while ($refClass !== false) {
                $methods = $refClass->getMethods();
                foreach ($methods as $method) {
                    if ($refClass->getName() !== $method->class) {
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
                                $renderComponent = new TemplateComponent();

                                $template = $annotation->getTemplate();
                                $name = $annotation->getName();

                                if ($name === null) {
                                    if ($isAlreadyDefinitionBaseTemplate) {
                                        $errorMsg = "Argument of @Template('" . $template . "') annotation is not enough. ";
                                        $errorMsg.= "Please add attribute 'name'.";
                                        throw new AnnotationException($errorMsg);
                                    }
                                    if ($annotation->isShared()) {
                                        $baseTemplate = STREAM_VIEW_SHARED . "/" . $template;
                                    } else {
                                        $baseTemplate = $this->templateDir . "/" . $template;
                                    }
                                    $isAlreadyDefinitionBaseTemplate = true;
                                } else {
                                    if ($annotation->isShared()) {
                                        $embedTemplates[$name] = STREAM_VIEW_SHARED . "/" . $template;
                                    } else {
                                        $embedTemplates[$name] = $this->templateDir . "/" . $template;
                                    }
                                }
                            }
                        }
                    }
                }

                $refClass = $refClass->getParentClass();
            }

            $templateInfo = ["base" => $baseTemplate, "embed" => $embedTemplates];

            return $templateInfo;

        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException("Class not found: " . $classpath);
        }
    }


    /**
     * テンプレートディレクトリ名を設定する
     * @param string テンプレートディレクトリ名
     */
    public function setTemplateDir($templateDir)
    {
        $this->templateDir = $this->camel2snake($templateDir);
    }
}
