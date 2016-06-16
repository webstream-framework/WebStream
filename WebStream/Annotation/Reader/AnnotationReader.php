<?php
namespace WebStream\Annotation\Reader;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IClass;
use WebStream\Annotation\Base\IMethod;
use WebStream\Annotation\Base\IMethods;
use WebStream\Annotation\Base\IProperty;
use WebStream\Annotation\Base\IRead;
use WebStream\Module\Container;
use WebStream\Delegate\ExceptionDelegator;
use WebStream\Exception\Extend\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * AnnotationReader
 * @author Ryuichi TANAKA.
 * @since 2014/05/10
 * @version 0.4
 */
class AnnotationReader
{
    /**
     * @var \ReflectionClass リフレクションクラスオブジェクト
     */
    private $refClass;

    /**
     * @var IAnnotatable インスタンス
     */
    private $instance;

    /**
     * @var Container コンテナ
     */
    private $container;

    /**
     * @var array 注入後のアノテーションリスト
     */
    private $injectedAnnotations;

    /**
     * @var Callable 読み込み時の例外
     */
    private $exception;

    /**
     * @var string 読み込み対象アノテーションクラスパス
     */
    private $annotationClasspath;

    /**
     * constructor
     * @param IAnnotatable アノテーション使用可能インスタンス
     * @param Container 依存コンテナ
     */
    public function __construct(IAnnotatable &$instance, Container $container)
    {
        $this->instance = $instance;
        $this->container = $container;
        $this->refClass = new \ReflectionClass($instance);
        $this->injectedAnnotations = [];
    }

    /**
     * 注入後の処理結果を返却する
     * @param array<mixed> 注入後の処理結果
     */
    public function getInjectedAnnotationInfo()
    {
        return $this->injectedAnnotations;
    }

    /**
     * 発生した例外を返却する
     * @param Callable 発生した例外
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * アノテーション情報を読み込む
     * @param stirng 読み込み対象アノテーションクラスパス
     * @throws DoctrineAnnotationException
     */
    public function read($annotationClasspath = null)
    {
        try {
            $this->annotationClasspath = $annotationClasspath;
            $this->readClass();
            $this->readMethods();
            $this->readProperties();
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e);
        }
    }

    /**
     * クラス情報を読み込む
     */
    private function readClass()
    {
        $reader = new DoctrineAnnotationReader();
        $refClass = $this->refClass;

        while ($refClass !== false) {
            $annotations = $reader->getClassAnnotations($refClass);

            // アノテーション定義がなければ次へ
            if (!empty($annotations)) {
                for ($i = 0, $count = count($annotations); $i < $count; $i++) {
                    $annotation = $annotations[$i];
                    $annotation->inject('logger', $this->container->logger);

                    if (!$annotation instanceof IClass) {
                        continue;
                    }

                    // アノテーションクラスパスが指定された場合、一致したアノテーション以外は読み込まない
                    if ($this->annotationClasspath !== null && $this->annotationClasspath !== get_class($annotation)) {
                        continue;
                    }

                    try {
                        $annotation->onClassInject($this->instance, $this->container, $refClass);
                    } catch (\Exception $e) {
                        if ($this->exception === null) {
                            $this->exception = new ExceptionDelegator($this->instance, $e);
                        }
                        continue;
                    }

                    $key = get_class($annotation);

                    // IReadを実装している場合、任意のデータを返却する
                    if ($annotation instanceof IRead) {
                        if (!array_key_exists($key, $this->injectedAnnotations)) {
                            $this->injectedAnnotations[$key] = [];
                        }
                        $this->injectedAnnotations[$key][] = $annotation->onInjected();
                    }
                }
            }

            $refClass = $refClass->getParentClass();
        }
    }

    /**
     * メソッド情報を読み込む
     */
    private function readMethods()
    {
        $reader = new DoctrineAnnotationReader();
        $refClass = $this->refClass;
        $executeMethod = $this->container->executeMethod;

        while ($refClass !== false) {
            foreach ($refClass->getMethods() as $method) {
                if ($refClass->getName() !== $method->class) {
                    continue;
                }

                $annotations = $reader->getMethodAnnotations($method);

                // アノテーション定義がなければ次へ
                if (empty($annotations)) {
                    continue;
                }

                for ($i = 0; $i < count($annotations); $i++) {
                    $annotation = $annotations[$i];
                    $annotation->inject('logger', $this->container->logger);

                    if (!$annotation instanceof IMethod && !$annotation instanceof IMethods) {
                        continue;
                    }

                    // アノテーションクラスパスが指定された場合、一致したアノテーション以外は読み込まない
                    if ($this->annotationClasspath !== null && $this->annotationClasspath !== get_class($annotation)) {
                        continue;
                    }

                    // IMethodを実装している場合、アクションメソッドのアノテーション以外は読み込まない
                    // PHPのメソッドは大文字小文字を区別しないため、そのまま比較するとルーティング解決結果と実際のメソッド名が合わないケースがある
                    // PHPの仕様に合わせてメソッド名の文字列比較は小文字に変換してから行う
                    if (strtolower($executeMethod) !== strtolower($method->name) && $annotation instanceof \WebStream\Annotation\Base\IMethod) {
                        continue;
                    }

                    try {
                        $annotation->onMethodInject($this->instance, $this->container, $method);
                    } catch (\Exception $e) {
                        if ($this->exception === null) {
                            $this->exception = new ExceptionDelegator($this->instance, $e, $executeMethod);
                        }
                        continue;
                    }

                    $key = get_class($annotation);

                    // IReadを実装している場合、任意のデータを返却する
                    if ($annotation instanceof IRead) {
                        if (!array_key_exists($key, $this->injectedAnnotations)) {
                            $this->injectedAnnotations[$key] = [];
                        }
                        $this->injectedAnnotations[$key][] = $annotation->onInjected();
                    }
                }
            }
            $refClass = $refClass->getParentClass();
        }
    }

    /**
     * プロパティ情報を読み込む
     */
    private function readProperties()
    {
        $reader = new DoctrineAnnotationReader();
        $refClass = $this->refClass;

        while ($refClass !== false) {
            foreach ($refClass->getProperties() as $property) {
                if ($refClass->getName() !== $property->class) {
                    continue;
                }

                $annotations = $reader->getPropertyAnnotations($property);

                // アノテーション定義がなければ次へ
                if (empty($annotations)) {
                    continue;
                }

                for ($i = 0, $count = count($annotations); $i < $count; $i++) {
                    $annotation = $annotations[$i];
                    $annotation->inject('logger', $this->container->logger);

                    if (!$annotation instanceof IProperty) {
                        continue;
                    }

                    // アノテーションクラスパスが指定された場合、一致したアノテーション以外は読み込まない
                    if ($this->annotationClasspath !== null && $this->annotationClasspath !== get_class($annotation)) {
                        continue;
                    }

                    try {
                        $annotation->onPropertyInject($this->instance, $this->container, $property);
                    } catch (\Exception $e) {
                        if ($this->exception === null) {
                            $this->exception = function () use ($e) {
                                throw $e;
                            };
                        }
                        continue;
                    }

                    $key = get_class($annotation);

                    // IReadを実装している場合、任意のデータを返却する
                    if ($annotation instanceof IRead) {
                        if (!array_key_exists($key, $this->injectedAnnotations)) {
                            $this->injectedAnnotations[$key] = [];
                        }
                        $this->injectedAnnotations[$key][] = $annotation->onInjected();
                    }
                }
            }
            $refClass = $refClass->getParentClass();
        }
    }
}
