<?php
namespace WebStream\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use WebStream\Exception\AnnotationException;

/**
 * AutowiredFactory
 * @author Ryuichi TANAKA.
 * @since 2013/09/18
 * @version 0.4
 */
class FilterFactory extends AnnotationFactory
{
    /**
     * @Override
     */
    public function classLoad()
    {
        $this->classLoader->load(["AbstractAnnotation", "Inject", "Filter"]);
    }

    /**
     * @Override
     */
    public function createInstance($classpath)
    {
        $reader = new AnnotationReader();
        $refClass = new \ReflectionClass($classpath);
        $methods = $refClass->getMethods();

        $componentClass = new \ReflectionClass("\WebStream\Annotation\FilterComponent");
        $componentInstance = $componentClass->newInstanceWithoutConstructor();

        $initializeMethod = $componentClass->getMethod("setInitializeMethod");
        $beforeQueueMethod = $componentClass->getMethod("setBeforeQueue");
        $afterQueueMethod = $componentClass->getMethod("setAfterQueue");
        $setInstance = $componentClass->getMethod("setInstance");
        $setInstance->invokeArgs($componentInstance, [$refClass]);
        $isInitializeDefined = false;

        foreach ($methods as $method) {
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
                            $initializeMethod->invokeArgs($componentInstance, [$method]);
                            $isInitializeDefined = true;
                        }
                        if ($annotation->enableBefore()) {
                            $beforeQueueMethod->invokeArgs($componentInstance, [$method]);
                        }
                        if ($annotation->enableAfter()) {
                            $afterQueueMethod->invokeArgs($componentInstance, [$method]);
                        }
                    }
                }
            }
        }

        $constructor = $componentClass->getConstructor();
        $constructor->invoke($componentInstance);

        return $componentInstance;
    }
}
