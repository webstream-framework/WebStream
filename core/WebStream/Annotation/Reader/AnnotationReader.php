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
    /**
     * @var \ReflectionClass リフレクションクラスオブジェクト
     */
    private $refClass;

    /**
     * @var object インスタンス
     */
    private $instance;

    /**
     * @var Container コンテナ
     */
    private $container;

    /**
     * @var array アノテーションリスト
     */
    private $annotations;

    /**
     * constructor
     * @param object インスタンス
     */
    public function __construct($instance)
    {
        // アノテーション処理対象インスタンスのリフレクションクラスオブジェクト
        $this->refClass = new \ReflectionClass($instance);
        $this->annotations = [];
    }

    /**
     * コンテナを設定
     * @param Container コンテナ
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * コンテナを返却
     * @return Container コンテナ
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * アノテーションを読み込む
     */
    public function read()
    {
        $this->readAnnotaion();
    }

    /**
     * リフレクションクラスオブジェクトを返却する
     * @return \ReflectionClass リフレクションクラスオブジェクト
     */
    public function getReflectionClass()
    {
        return $this->refClass;
    }

    /**
     * アノテーションコンテナを返却する
     * @return Container アノテーションコンテナ
     */
    public function getAnnotation($classpath)
    {
        return array_key_exists($classpath, $this->annotations) ? $this->annotations[$classpath] : null;
    }

    /**
     * アノテーション情報を読み込む
     */
    private function readAnnotaion()
    {
        try {
            $this->readClass();
            $this->readProperties();
            $this->readMethods();
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
            if (!empty($annotations)) {
                // @Injectは先頭に定義されていなければならない
                if (!$annotations[0] instanceof \WebStream\Annotation\Inject) {
                    throw new AnnotationException("@Inject must be defined at the top of class.");
                }
                for ($i = 1; $i < count($annotations); $i++) {
                    $annotation = $annotations[$i];
                    $classpath = get_class($annotation);
                    // リストで初期化
                    if (!array_key_exists($classpath, $this->annotations)) {
                        $this->annotations[$classpath] = [];
                    }
                    $this->annotations[$classpath][$refClass->getName()] = $annotation->getAnnotationContainer();
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

    /**
     * プロパティ情報を読み込む
     */
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
}
