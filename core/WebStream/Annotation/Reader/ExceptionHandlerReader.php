<?php
namespace WebStream\Annotation\Reader;

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
    /** アノテーションコンテナ */
    private $annotation;

    /** ハンドリング例外 */
    private $handledException;

    /** ハンドリングメソッドリスト */
    private $handleMethods;

    /**
     * {@inheritdoc}
     */
    public function onRead()
    {
        $this->annotation = $this->reader->getAnnotation("WebStream\Annotation\ExceptionHandler");
        $this->handleMethods = [];
    }

    /**
     * 例外クラスインスタンスを設定する
     * @param \Exception 例外クラスインスタンス
     */
    public function inject(\Exception $handledException)
    {
        $this->handledException = $handledException;
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
                            $exceptionClass = $exceptionContainer->get("value");
                            if (is_a($this->handledException, $exceptionClass)) {
                                $this->handleMethods[] = $refMethod->name;
                            }
                        }
                    }

                }
                $refClass = $refClass->getParentClass();
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e);
        }
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
