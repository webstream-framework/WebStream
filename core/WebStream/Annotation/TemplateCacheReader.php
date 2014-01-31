<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;
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
    public function readAnnotation($refClass, $method, $arguments)
    {
        $reader = new DoctrineAnnotationReader();

        try {
            while ($refClass !== false) {
                if ($refClass->hasMethod($method)) {
                    $refMethod = $refClass->getMethod($method);
                    if ($reader->getMethodAnnotation($refMethod, "\WebStream\Annotation\Inject")) {
                        $annotation = $reader->getMethodAnnotation($refMethod, "\WebStream\Annotation\TemplateCache");
                        $expire = $annotation->getExpire();
                        if (!preg_match("/^[1-9]{1}[0-9]{0,}$/", $expire)) {
                            $errorMsg = "Expire value is not integer: @TemplateCache(expire=\"" . strval($expire) . "\")";
                            throw new AnnotationException($errorMsg);
                        } else {
                            $expire = intval($expire);
                            if ($expire <= 0) {
                                $errorMsg = "Expire value is out of integer range: @TemplateCache(expire=" . strval($expire) . ")";
                                throw new AnnotationException($errorMsg);
                            } elseif ($expire >= PHP_INT_MAX) {
                                Logger::warn("Expire value converted the maximum of PHP Integer.");
                            }
                            $this->expire = $expire;
                        }
                        break;
                    }
                }

                $refClass = $refClass->getParentClass();
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
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
