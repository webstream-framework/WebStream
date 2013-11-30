<?php
namespace WebStream\Annotation;

use WebStream\Core\CoreController;
use WebStream\Module\Container;
use WebStream\Exception\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;

/**
 * FilterReader
 * @author Ryuichi TANAKA.
 * @since 2013/09/18
 * @version 0.4
 */
class FilterReader extends AnnotationReader
{
    /** component instance */
    private $component;

    /**
     * @Override
     */
    public function readAnnotation($refClass, $method, $arguments)
    {
        $reader = new DoctrineAnnotationReader();
        $component = new FilterComponent();
        $instance = $this->instance;

        $isInitializeDefined = false;
        $initializeContainer = new Container();
        $beforeContainer = new Container();
        $afterContainer = new Container();
        $i = 0;
        $j = 0;

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
                        if ($annotation instanceof Filter) {
                            if ($annotation->enableInitialize()) {
                                // @Initializeは複数定義許可しない
                                if ($isInitializeDefined) {
                                    throw new AnnotationException("Can not multiple define @Filter(\"Initialize\") at method.");
                                }
                                $initializeContainer->registerAsLazy(0, function() use ($instance, $method) {
                                    $method->invoke($instance);
                                });
                                $isInitializeDefined = true;
                            }
                            if ($annotation->enableBefore()) {
                                $beforeContainer->registerAsLazy($i++, function() use ($instance, $method) {
                                    $method->invoke($instance);
                                });
                            }
                            if ($annotation->enableAfter()) {
                                $afterContainer->registerAsLazy($j++, function() use ($instance, $method) {
                                    $method->invoke($instance);
                                });
                            }
                        }
                    }
                }
            }

            $refClass = $refClass->getParentClass();
        }

        $component->setInitializeContainer($initializeContainer);
        $component->setBeforeContainer($beforeContainer);
        $component->setAfterContainer($afterContainer);

        $this->component = $component;
    }

    /**
     * コンポーネントインスタンスを返却する
     * @return object コンポーネントインスタンス
     */
    public function getComponent()
    {
        return $this->component;
    }
}
