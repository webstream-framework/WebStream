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
    public function createReflectionClassInstance($classpath)
    {
        $reader = new AnnotationReader();
        $refClass = new \ReflectionClass($classpath);
        $properties = $refClass->getProperties();
        $instances = [];

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
                    // @Type,@Valueが同時に指定された場合は@Typeだけ見る
                    if ($annotation instanceof Type || $annotation instanceof Value) {
                        $instances[$property->getName()] = $annotation;
                    }
                }
            }
        }

        return $instances;
    }
}
