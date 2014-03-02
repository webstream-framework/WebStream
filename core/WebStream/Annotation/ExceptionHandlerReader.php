<?php
namespace WebStream\Annotation;

use WebStream\Exception\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

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

        try {
            while ($refClass !== false) {
                $refMethods = $refClass->getMethods();
                foreach ($refMethods as $refMethod) {
                    if ($reader->getMethodAnnotation($refMethod, "\WebStream\Annotation\Inject")) {
                        $annotations = $reader->getMethodAnnotations($refMethod);
                        foreach ($annotations as $annotation) {
                            if ($annotation instanceof ExceptionHandler) {
                                $classpathList = $annotation->getExceptionClasspathList();
                                foreach ($classpathList as $classpath) {
                                    // メソッドに同じ例外(祖先含む)が指定された場合、無視する
                                    if (is_a($this->handledException, $classpath)) {
                                        $this->handleMethods[] = $refMethod->name;
                                    }
                                }
                            }
                        }
                    }
                }

                $refClass = $refClass->getParentClass();
            }

            $this->handleMethods = array_unique($this->handleMethods);

        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
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
