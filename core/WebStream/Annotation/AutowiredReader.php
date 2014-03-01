<?php
namespace WebStream\Annotation;

use WebStream\Exception\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * AutowiredReader
 * @author Ryuichi TANAKA.
 * @since 2013/09/18
 * @version 0.4.1
 */
class AutowiredReader extends AnnotationReader
{
    /**
     * Override
     */
    public function readAnnotation($refClass, $method, $arguments)
    {
        $reader = new DoctrineAnnotationReader();

        try {
            $constructor = $refClass->getConstructor();
            while ($refClass !== false) {
                $properties = $refClass->getProperties();
                foreach ($properties as $property) {

                    if ($reader->getPropertyAnnotation($property, "\WebStream\Annotation\Autowired")) {
                        $annotation = $reader->getPropertyAnnotation($property, "\WebStream\Annotation\Type") ?:
                            $reader->getPropertyAnnotation($property, "\WebStream\Annotation\Value");
                        if ($annotation !== null) {
                            if ($property->isPrivate() || $property->isProtected()) {
                                $property->setAccessible(true);
                            }
                            $property->setValue($this->instance, $annotation->getValue());
                        }
                    }
                }

                $refClass = $refClass->getParentClass();
            }

            if ($constructor !== null) {
                $constructor->invokeArgs($this->instance, [$arguments]);
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        }
    }
}
