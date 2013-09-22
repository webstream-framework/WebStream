<?php
namespace WebStream\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;

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
    }

    /**
     * @Override
     */
    public function createInstance($classpath, $arguments)
    {
        $reader = new AnnotationReader();
        $refClass = new \ReflectionClass($classpath);
        $refInstance = $refClass->newInstanceWithoutConstructor();
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

        $constructor = $refClass->getConstructor();
        if ($constructor !== null) {
            $constructor->invoke($refInstance);
        }

        return $refInstance;
    }
}
