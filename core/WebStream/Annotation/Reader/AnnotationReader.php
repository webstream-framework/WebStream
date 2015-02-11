<?php
namespace WebStream\Annotation\Reader;

use WebStream\Core\CoreInterface;
use WebStream\Module\Container;
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
     * @var CoreInterface インスタンス
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
     * constructor
     * @param CoreInterface インスタンス
     * @param Container 依存コンテナ
     */
    public function __construct(CoreInterface &$instance, Container $container)
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
     * アノテーション情報を読み込む
     */
    public function read()
    {
        try {
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
                // @Injectは先頭に定義されていなければならない
                if (!$annotations[0] instanceof \WebStream\Annotation\Inject) {
                    throw new AnnotationException("@Inject must be defined at the top of class.");
                }

                for ($i = 1; $i < count($annotations); $i++) {
                    $annotation = $annotations[$i];
                    $annotation->onClassInject($this->instance, $this->container, $refClass);
                    $key = get_class($annotation);

                    // IReadを実装している場合、任意のデータを返却する
                    if ($annotation instanceof \WebStream\Annotation\Base\IRead) {
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
        $actionMethod = $this->container->router->action();

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

                // @Injectは先頭に定義されていなければならない
                if (!$annotations[0] instanceof \WebStream\Annotation\Inject) {
                    throw new AnnotationException("@Inject must be defined at the top of method.");
                }

                for ($i = 1; $i < count($annotations); $i++) {
                    $annotation = $annotations[$i];

                    // IMethodを実装している場合、アクションメソッドのアノテーション以外は読み込まない
                    if ($actionMethod !== $method->name && $annotation instanceof \WebStream\Annotation\Base\IMethod) {
                        continue;
                    }

                    $annotation->onMethodInject($this->instance, $this->container, $method);
                    $key = get_class($annotation);

                    // IReadを実装している場合、任意のデータを返却する
                    if ($annotation instanceof \WebStream\Annotation\Base\IRead) {
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
                // @Injectは先頭に定義されていなければならない
                if (!$annotations[0] instanceof \WebStream\Annotation\Inject) {
                    throw new AnnotationException("@Inject must be defined at the top of property.");
                }

                for ($i = 1; $i < count($annotations); $i++) {
                    $annotation = $annotations[$i];
                    $annotation->onPropertyInject($this->instance, $property);
                }
            }
            $refClass = $refClass->getParentClass();
        }
    }
}
