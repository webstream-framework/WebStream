<?php
namespace WebStream\Annotation;

use WebStream\Exception\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * AutowiredFactory
 * @author Ryuichi TANAKA.
 * @since 2013/09/18
 * @version 0.4
 */
class AutowiredFactory extends AnnotationFactory
{
    /**
     * @Override
     */
    public function classLoad()
    {
        $this->classLoader->load(["AbstractAnnotation", "Autowired", "Type", "Value"]);
        $this->classLoader->load("Doctrine/Common/Annotations/AnnotationException");
    }

    /**
     * @Override
     */
    public function createInstance($classpath, $arguments)
    {
        $reader = new AnnotationReader();
        try {
            $refClass = new \ReflectionClass($classpath);
            $refInstance = $refClass->newInstanceWithoutConstructor();
            $constructor = $refClass->getConstructor();

            while ($refClass !== false) {
                $properties = $refClass->getProperties();
                foreach ($properties as $property) {
                    $annotations = $reader->getPropertyAnnotations($property);

                    $isAutowired = false;
                    foreach ($annotations as $annotation) {
                        if ($annotation instanceof Autowired) {
                            $isAutowired = true;
                        }
                    }

                    if ($isAutowired) {
                        foreach ($annotations as $annotation) {
                            if ($annotation instanceof Type || $annotation instanceof Value) {
                                if ($property->isPrivate() || $property->isProtected()) {
                                    $property->setAccessible(true);
                                }
                                $property->setValue($refInstance, $annotation->getValue());
                            }
                        }
                    }
                }

                $refClass = $refClass->getParentClass();
            }

            if ($constructor !== null) {
                $constructor->invoke($refInstance);
            }

            return $refInstance;

        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        }
    }
}
