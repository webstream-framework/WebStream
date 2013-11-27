<?php
namespace WebStream\Annotation;

use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;

/**
 * ExceptionHandlerReader
 * @author Ryuichi TANAKA.
 * @since 2013/11/22
 * @version 0.4
 */
class ExceptionHandlerReader extends AnnotationReader
{
    /** ハンドリング例外 */
    private $handledException;

    /** ハンドリングフラグ */
    private $handleMethods = [];

    /**
     * @Override
     */
    public function readAnnotation($refClass, $methodName, $arguments)
    {
        $reader = new DoctrineAnnotationReader();
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
                        if ($annotation instanceof ExceptionHandler) {
                            $classpath = $annotation->getExceptionClasspath();
                            if (is_a($this->handledException, $classpath)) {
                                $this->handleMethods[] = $method->name;
                            }
                        }
                    }
                }
            }

            $refClass = $refClass->getParentClass();
        }
    }

    /**
     * ハンドリング対象の例外を設定する
     * @return object 例外オブジェクト
     */
    public function setHandledException(\Exception $handledException)
    {
        $this->handledException = $handledException;
    }

    /**
     * ハンドリングメソッドを返却する
     * @return array ハンドリングメソッド
     */
    public function getHandleMethods()
    {
        return $this->handleMethods;
    }
}
