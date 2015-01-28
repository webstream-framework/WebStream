<?php
namespace WebStream\Annotation\Reader;

use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Exception\Extend\AnnotationException;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * ExceptionHandlerReader
 * @author Ryuichi TANAKA.
 * @since 2013/10/30
 * @version 0.4
 */
class ExceptionHandlerReader extends AbstractAnnotationReader implements AnnotationReadInterface
{
    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotation;

    /**
     * {@inheritdoc}
     */
    public function onRead()
    {
        $this->annotation = $this->reader->getAnnotation("WebStream\Annotation\ExceptionHandler");
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $annotationContainer = new AnnotationContainer();

        if ($this->annotation === null) {
            return $annotationContainer;
        }

        $refClass = $this->reader->getReflectionClass();
        $exceptions = [];

        try {
            while ($refClass !== false) {
                $refMethods = $refClass->getMethods();
                foreach ($refMethods as $refMethod) {
                    // アクションメソッド自体もフィルタの対象(Railsの仕様に合わせる)
                    // 重複して実行しないようにする
                    if ($refClass->getName() !== $refMethod->class) {
                        continue;
                    }

                    $actionAnnotationKey = $refMethod->class . "#" . $refMethod->name;
                    if (array_key_exists($actionAnnotationKey, $this->annotation)) {
                        $exceptionContainers = $this->annotation[$actionAnnotationKey];

                        foreach ($exceptionContainers as $exceptionContainer) {
                            $exceptionClassList = $exceptionContainer->get("value");
                            if (!is_array($exceptionClassList)) {
                                $exceptionClassList = [$exceptionClassList];
                            }
                            if (!array_key_exists($refMethod->class, $exceptions)) {
                                $exceptions[$refMethod->class] = [];
                            }
                            if (!array_key_exists($refMethod->name, $exceptions[$refMethod->class])) {
                                $exceptions[$refMethod->class][$refMethod->name] = [];
                            }

                            // 複数の@ExceptionHandlerが指定された場合を考慮して配列
                            $exceptions[$refMethod->class][$refMethod->name][] = $exceptionClassList;
                        }
                    }
                }

                $refClass = $refClass->getParentClass();
            }

            $annotationContainer->exceptions = $exceptions;

        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e);
        }

        return $annotationContainer;
    }
}
