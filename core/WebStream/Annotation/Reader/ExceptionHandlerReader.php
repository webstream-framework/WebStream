<?php
namespace WebStream\Annotation\Reader;

use WebStream\Exception\Extend\AnnotationException;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * TemplateCache
 * @author Ryuichi TANAKA.
 * @since 2013/10/30
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class ExceptionHandlerReader extends AbstractAnnotationReader
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
    public function execute()
    {
        if ($this->annotation === null) {
            return;
        }

        $refClass = $this->reader->getReflectionClass();
        $handleMethods = [];

        try {
            while ($refClass !== false) {
                $refMethods = $refClass->getMethods();
                foreach ($refMethods as $refMethod) {
                    // アクションメソッド自体もフィルタの対象(Railsの仕様に合わせる)
                    // 重複して実行しないようにする
                    if ($refClass->getName() !== $refMethod->class) {
                        continue;
                    }

                    $actionAnnotationKey = $refClass->getName() . "#" . $refMethod->getName();
                    if (array_key_exists($actionAnnotationKey, $this->annotation)) {
                        $exceptionContainers = $this->annotation[$actionAnnotationKey];
                        foreach ($exceptionContainers as $exceptionContainer) {
                            $exceptionClassList = $exceptionContainer->get("value");
                            if (!is_array($exceptionClassList)) {
                                $exceptionClassList = [$exceptionClassList];
                            }
                            foreach ($exceptionClassList as $exceptionClass) {
                                if (is_a($this->instance, $exceptionClass)) {
                                    $handleMethods[] = $refMethod->name;
                                }
                            }
                        }
                    }
                }

                $refClass = $refClass->getParentClass();
            }

            $this->annotationAttributes->handleMethods = $handleMethods;
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e);
        }
    }
}
