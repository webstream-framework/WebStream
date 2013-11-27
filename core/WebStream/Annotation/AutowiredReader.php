<?php
namespace WebStream\Annotation;

use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;

/**
 * AutowiredReader
 * @author Ryuichi TANAKA.
 * @since 2013/09/18
 * @version 0.4.1
 */
class AutowiredReader extends AnnotationReader
{
    /** reveiver */
    private $reveiver;

    /**
     * @Override
     */
    public function readAnnotation($refClass, $method, $arguments)
    {
        $reader = new DoctrineAnnotationReader();
        $receiver = $refClass->newInstanceWithoutConstructor();
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
                            $property->setValue($receiver, $annotation->getValue());
                        }
                    }
                }
            }

            $refClass = $refClass->getParentClass();
        }

        if ($constructor !== null) {
            $constructor->invokeArgs($receiver, [$arguments]);
        }

        $this->receiver = $receiver;
    }

    /**
     * Autowiredしたレシーバを返却する
     * @return object レシーバ
     */
    public function getReceiver()
    {
        return $this->receiver;
    }
}
