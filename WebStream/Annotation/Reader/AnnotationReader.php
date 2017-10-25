<?php
namespace WebStream\Annotation\Reader;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IClass;
use WebStream\Annotation\Base\IMethod;
use WebStream\Annotation\Base\IMethods;
use WebStream\Annotation\Base\IProperty;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Reader\Extend\ExtendReader;
use WebStream\Container\Container;
use WebStream\DI\Injector;
use WebStream\Exception\Delegate\ExceptionDelegator;
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
    use Injector;

    /**
     * @var \ReflectionClass リフレクションクラスオブジェクト
     */
    // private $refClass;

    /**
     * @var IAnnotatable インスタンス
     */
    private $instance;

    /**
     * @var Logger ロガー
     */
    // private $logger;

    /**
     * @var Container コンテナ
     */
    // private $container;

    /**
     * @var array<string> 読み込み可能アノテーション情報
     */
    private $readableMap;

    /**
     * @var array<ExtendReader> 拡張アノテーションリーダー
     */
    private $extendReaderMap;

    /**
     * @var array<string> アノテーション情報リスト
     */
    private $annotationInfoList;

    /**
     * @var array<string> アノテーション情報リスト(拡張リーダー処理済み)
     */
    private $annotationInfoExtendList;

    /**
     * @var callable 読み込み時の例外
     */
    private $exception;

    /**
     * @var string 読み込み対象アノテーションクラスパス
     */
    // private $annotationClasspath;

    /**
     * @var string アクションメソッド
     */
    private $actionMethod;

    /**
     * constructor
     * @param IAnnotatable ターゲットインスタンス
     * @param Container 依存コンテナ
     */
    public function __construct(IAnnotatable $instance)
    {
        $this->initialize();
        $this->instance = $instance;
    }

    /**
     * 初期化処理
     */
    private function initialize()
    {
        $this->readableMap = [];
        $this->extendReaderMap = [];
        $this->annotationInfoList = [];
        $this->annotationInfoExtendList = [];
    }

    /**
     * アノテーション情報リストを返却する
     * @param array<mixed> アノテーション情報リスト
     */
    public function getAnnotationInfoList(): array
    {
        if (empty($this->extendReaderMap)) {
            return $this->annotationInfoList;
        }

        if (!empty($this->annotationInfoExtendList)) {
            return $this->annotationInfoExtendList;
        }

        foreach ($this->annotationInfoList as $key => $annotationInfo) {
            $readerClasspath = $this->extendReaderMap[$key];
            $reader = new $readerClasspath();
            $reader->read($annotationInfo);
            $this->annotationInfoExtendList[$key] = $reader->getAnnotationInfo();
        }

        return $this->annotationInfoExtendList;
    }

    /**
     * 発生した例外を返却する
     * @param ExceptionDelegator 発生した例外
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * アクションメソッドを設定する
     * @param string アクションメソッド
     */
    public function setActionMethod(string $actionMethod)
    {
        $this->actionMethod = $actionMethod;
    }

    /**
     * 読み込み可能アノテーション情報を設定する
     * @param string アノテーションクラスパス
     * @param Container アノテーションクラス依存コンテナ
     */
    public function readable(string $classpath, Container $container = null)
    {
        $this->readableMap[$classpath] = $container;
    }

     /**
      * 拡張アノテーションリーダーを設定する
      * @param string アノテーションクラスパス
      * @param string 拡張アノテーションリーダークラスパス
      */
    public function useExtendReader(string $annotationClasspath, string $readerClasspath)
    {
        $this->extendReaderMap[$annotationClasspath] = $readerClasspath;
    }

    /**
     * アノテーション情報を読み込む
     */
    public function read()
    {
        try {
            $this->readClass();
            $this->readMethod();
            $this->readProperty();
        } catch (DoctrineAnnotationException $e) {
            $this->initialize();
            throw new AnnotationException($e);
        }
    }

    /**
     * クラス情報を読み込む
     */
    public function readClass()
    {
        $reader = new DoctrineAnnotationReader();
        $refClass = new \ReflectionClass($this->instance);

        while ($refClass !== false) {
            $annotations = $reader->getClassAnnotations($refClass);

            if (!empty($annotations)) {
                for ($i = 0, $count = count($annotations); $i < $count; $i++) {
                    $annotation = $annotations[$i];

                    if (!$annotation instanceof IClass) {
                        continue;
                    }

                    $key = get_class($annotation);
                    if (!array_key_exists($key, $this->readableMap)) {
                        continue;
                    }

                    $container = $this->readableMap[$key];

                    try {
                        $annotation->onClassInject($this->instance, $refClass, $container);
                    } catch (\Exception $e) {
                        if ($this->exception === null) {
                            $this->exception = new ExceptionDelegator($this->instance, $e);
                        }
                        continue;
                    }

                    // IReadを実装している場合、任意のデータを返却する
                    if ($annotation instanceof IRead) {
                        if (!array_key_exists($key, $this->annotationInfoList)) {
                            $this->annotationInfoList[$key] = [];
                        }
                        $this->annotationInfoList[$key][] = $annotation->getAnnotationInfo();
                    }
                }
            }

            $refClass = $refClass->getParentClass();
        }
    }

    /**
     * メソッド情報を読み込む
     */
    public function readMethod()
    {
        $reader = new DoctrineAnnotationReader();
        $refClass = new \ReflectionClass($this->instance);

        while ($refClass !== false) {
            foreach ($refClass->getMethods() as $refMethod) {
                if ($refClass->getName() !== $refMethod->class) {
                    continue;
                }

                $annotations = $reader->getMethodAnnotations($refMethod);
                if (empty($annotations)) {
                    continue;
                }

                for ($i = 0, $count = count($annotations); $i < $count; $i++) {
                    $annotation = $annotations[$i];

                    if (!$annotation instanceof IMethod && !$annotation instanceof IMethods) {
                        continue;
                    }

                    // IMethodを実装している場合、アクションメソッドのアノテーション以外は読み込まない
                    // PHPのメソッドは大文字小文字を区別しないため、そのまま比較するとルーティング解決結果と実際のメソッド名が合わないケースがある
                    // PHPの仕様に合わせてメソッド名の文字列比較は小文字に変換してから行う
                    if ($annotation instanceof IMethod && strtolower($this->actionMethod) !== strtolower($refMethod->name)) {
                        continue;
                    }

                    // 読み込み可能なアノテーション以外は読み込まない
                    $key = get_class($annotation);
                    if (!array_key_exists($key, $this->readableMap)) {
                        continue;
                    }

                    $container = $this->readableMap[$key];

                    try {
                        $annotation->onMethodInject($this->instance, $refMethod, $container);
                    } catch (\Exception $e) {
                        if ($this->exception === null) {
                            $this->exception = new ExceptionDelegator($this->instance, $e, $this->actionMethod);
                        }
                        continue;
                    }

                    // IReadを実装している場合、任意のデータを返却する
                    if ($annotation instanceof IRead) {
                        if (!array_key_exists($key, $this->annotationInfoList)) {
                            $this->annotationInfoList[$key] = [];
                        }
                        $this->annotationInfoList[$key][] = $annotation->getAnnotationInfo();
                    }
                }
            }

            $refClass = $refClass->getParentClass();
        }

        // 拡張リーダー処理結果をクリアする
        $this->annotationInfoExtendList = [];
    }

    /**
     * プロパティ情報を読み込む
     */
    private function readProperty()
    {
        $reader = new DoctrineAnnotationReader();
        $refClass = $this->refClass;

        while ($refClass !== false) {
            foreach ($refClass->getProperties() as $refProperty) {
                if ($refClass->getName() !== $refProperty->class) {
                    continue;
                }

                $annotations = $reader->getPropertyAnnotations($refProperty);

                // アノテーション定義がなければ次へ
                if (empty($annotations)) {
                    continue;
                }

                for ($i = 0, $count = count($annotations); $i < $count; $i++) {
                    $annotation = $annotations[$i];
                    // $annotation->inject('logger', $this->container->logger);

                    if (!$annotation instanceof IProperty) {
                        continue;
                    }

                    $key = get_class($annotation);
                    if (!array_key_exists($key, $this->readableMap)) {
                        continue;
                    }

                    $container = $this->readableMap[$key];

                    try {
                        $annotation->onPropertyInject($this->instance, $refProperty, $container);
                    } catch (\Exception $e) {
                        if ($this->exception === null) {
                            $this->exception = new ExceptionDelegator($this->instance, $e);
                        }
                        continue;
                    }

                    // IReadを実装している場合、任意のデータを返却する
                    if ($annotation instanceof IRead) {
                        if (!array_key_exists($key, $this->annotationInfoList)) {
                            $this->annotationInfoList[$key] = [];
                        }
                        $this->annotationInfoList[$key][] = $annotation->onInjected();
                    }
                }
            }

            $refClass = $refClass->getParentClass();
        }
    }
}
