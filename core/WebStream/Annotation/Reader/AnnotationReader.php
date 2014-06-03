<?php
namespace WebStream\Annotation\Reader;

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
    /** リフレクションクラス */
    private $refClass;

    /** インスタンスオブジェクト */
    private $instance;

    /** DIコンテナ */
    private $container;

    /** アノテーション情報 */
    private $annotations = [];

    public function __construct($instance)
    {
        // アノテーション処理対象インスタンスのリフレクションクラスオブジェクト
        $this->refClass = new \ReflectionClass($instance);
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function read()
    {
        $this->readAnnotaion();
    }

    public function getReflectionClass()
    {
        return $this->refClass;
    }

    public function getAnnotation($classpath)
    {
        return array_key_exists($classpath, $this->annotations) ? $this->annotations[$classpath] : null;
    }

    private function readAnnotaion()
    {
        try {
            $this->readProperties();
            $this->readMethods();
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        }
    }

    private function readProperties()
    {
        $reader = new DoctrineAnnotationReader();
        $refClass = $this->refClass;

        while ($refClass !== false) {
            foreach ($refClass->getProperties() as $property) {
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
                    $classpath = get_class($annotation);
                    // リストで初期化
                    if (!array_key_exists($classpath, $this->annotations)) {
                        $this->annotations[$classpath] = [];
                    }
                    // プロパティの場合は複数指定しても後勝ちでリストにはしない
                    $this->annotations[$classpath][$refClass->getName() . "." . $property->getName()] = $annotation->getAnnotationContainer();
                }
            }
            $refClass = $refClass->getParentClass();
        }
    }

    private function readMethods()
    {
        $reader = new DoctrineAnnotationReader();
        $refClass = $this->refClass;

        while ($refClass !== false) {
            foreach ($refClass->getMethods() as $method) {
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
                    $classpath = get_class($annotation);
                    // リストで初期化
                    if (!array_key_exists($classpath, $this->annotations)) {
                        $this->annotations[$classpath] = [];
                    }
                    // 同じアノテーションが指定された場合はリストにする
                    $ca = $refClass->getName() . "#" . $method->getName();
                    if (!array_key_exists($ca, $this->annotations[$classpath])) {
                        $this->annotations[$classpath][$ca] = [];
                    }
                    $this->annotations[$classpath][$ca][] = $annotation->getAnnotationContainer();
                }
            }
            $refClass = $refClass->getParentClass();
        }
    }
}
