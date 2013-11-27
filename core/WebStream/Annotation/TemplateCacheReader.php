<?php
namespace WebStream\Annotation;

use WebStream\Exception\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;

/**
 * TemplateCacheReader
 * @author Ryuichi TANAKA.
 * @since 2013/10/30
 * @version 0.4
 */
class TemplateCacheReader extends AnnotationReader
{
    /** 有効期限 */
    private $expire;

    /**
     * @Override
     */
    public function readAnnotation($refClass, $methodName, $arguments)
    {
        $reader = new DoctrineAnnotationReader();
        while ($refClass !== false) {
            $methods = $refClass->getMethods();
            foreach ($methods as $method) {
                if ($refClass->getName() !== $method->class || $methodName !== $method->name) {
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
                        if ($annotation instanceof TemplateCache) {
                            $expire = $annotation->getExpire();
                            if (!is_int($expire)) {
                                $errorMsg = "Expire value is not integer: @TemplateCache(expire=\"" . strval($expire) . "\")";
                            } elseif ($expire <= 0 || $expire > PHP_INT_MAX) {
                                $errorMsg = "Expire value is out of integer range: @TemplateCache(expire=" . strval($expire) . ")";
                                throw new AnnotationException($errorMsg);
                            }
                            $this->expire = $expire;
                            break;
                        }
                    }
                }
            }

            $refClass = $refClass->getParentClass();
        }
    }

    /**
     * 有効期限を返却する
     * @return integer 有効期限
     */
    public function getExpire()
    {
        return $this->expire;
    }
}
