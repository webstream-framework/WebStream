<?php
namespace WebStream\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use WebStream\Exception\AnnotationException;
use WebStream\Exception\ClassNotFoundException;

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
        try {
            $refClass = new \ReflectionClass($classpath);

            $componentClass = new \ReflectionClass("\WebStream\Annotation\FilterComponent");
            $componentInstance = $componentClass->newInstanceWithoutConstructor();

            $initializeMethod = $componentClass->getMethod("setInitializeMethod");
            $beforeQueueMethod = $componentClass->getMethod("setBeforeQueue");
            $afterQueueMethod = $componentClass->getMethod("setAfterQueue");
            $setInstance = $componentClass->getMethod("setInstance");
            $setInstance->invokeArgs($componentInstance, [$refClass]);
            $isInitializeDefined = false;

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

                $refClass = $refClass->getParentClass();
            }

            $constructor = $componentClass->getConstructor();
            $constructor->invoke($componentInstance);

            return $componentInstance;

        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException("Class not found: " . $classpath);
        }
    }
}
