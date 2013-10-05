<?php
namespace WebStream\Annotation;

use WebStream\Module\Container;
use WebStream\Exception\AnnotationException;
use WebStream\Exception\ClassNotFoundException;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * FilterReader
 * @author Ryuichi TANAKA.
 * @since 2013/09/18
 * @version 0.4
 */
class FilterReader extends AnnotationReader
{
    /**
     * @Override
     */
    public function classLoad()
    {
        $this->classLoader->load(["AbstractAnnotation", "Inject", "Filter", "Render"]);
        $this->classLoader->load("Doctrine/Common/Annotations/AnnotationException");
    }

    /**
     * @Override
     */
    public function readAnnotation($classpath, $method)
    {
        $reader = new DoctrineAnnotationReader();
        try {
            $refClass = new \ReflectionClass($classpath);
            $component = new FilterComponent();

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

                                    $initializeContainer->registerAsLazy(0, function() use ($method) {
                                        $refClass = new \ReflectionClass($method->class);
                                        $instance = $refClass->newInstanceWithoutConstructor();
                                        $refMethod = $refClass->getMethod($method->name);
                                        $refMethod->invoke($instance);
                                    });
                                    $isInitializeDefined = true;
                                }
                                if ($annotation->enableBefore()) {
                                    // }
                                    $beforeContainer->registerAsLazy($i++, function() use ($method) {
                                        $refClass = new \ReflectionClass($method->class);
                                        $instance = $refClass->newInstanceWithoutConstructor();
                                        $refMethod = $refClass->getMethod($method->name);
                                        $refMethod->invoke($instance);
                                    });
                                }
                                if ($annotation->enableAfter()) {

                                    $afterContainer->registerAsLazy($j++, function() use ($method) {
                                        $refClass = new \ReflectionClass($method->class);
                                        $instance = $refClass->newInstanceWithoutConstructor();
                                        $refMethod = $refClass->getMethod($method->name);
                                        $refMethod->invoke($instance);
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


            return $component;

        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException("Class not found: " . $classpath);
        }
    }
}
