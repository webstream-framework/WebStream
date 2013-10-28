<?php
namespace WebStream\Annotation;

use WebStream\Module\Container;
use WebStream\Core\CoreController;
use WebStream\Exception\AnnotationException;
use WebStream\Exception\ClassNotFoundException;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * FilterReader
 * @author Ryuichi TANAKA.
 * @since 2013/09/18
 * @version 0.4
 */
class FilterReader extends AnnotationReader
{
    /** reveiver */
    private $receiver;

    /** component instance */
    private $component;

    /**
     * @Override
     */
    public function readAnnotation($refClass, $method, $arguments)
    {
        $reader = new DoctrineAnnotationReader();
        try {
            $component = new FilterComponent();
            $receiver = $this->receiver;

            $isInitializeDefined = false;
            $initializeContainer = new Container();
            $beforeContainer = new Container();
            $afterContainer = new Container();
            $i = 0;
            $j = 0;

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
                            if ($annotation instanceof Filter) {
                                if ($annotation->enableInitialize()) {
                                    // @Initializeは複数定義許可しない
                                    if ($isInitializeDefined) {
                                        throw new AnnotationException("Can not multiple define @Filter(\"Initialize\") at method.");
                                    }
                                    $initializeContainer->registerAsLazy(0, function() use ($receiver, $method) {
                                        $receiver->{$method->name}();
                                    });
                                    $isInitializeDefined = true;
                                }
                                if ($annotation->enableBefore()) {
                                    $beforeContainer->registerAsLazy($i++, function() use ($receiver, $method) {
                                        $receiver->{$method->name}();
                                    });
                                }
                                if ($annotation->enableAfter()) {
                                    $afterContainer->registerAsLazy($j++, function() use ($receiver, $method) {
                                        $receiver->{$method->name}();
                                    });
                                }
                            }
                        }
                    }
                }

                $refClass = $refClass->getParentClass();
            }

            $component->setInitializeContainer($initializeContainer);
            $component->setBeforeContainer($beforeContainer);
            $component->setAfterContainer($afterContainer);

            $this->component = $component;

        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException("Class not found: " . $classpath);
        }
    }

    /**
     * 実行するControllerのレシーバを設定する
     * @param object レシーバ
     */
    public function setReceiver(CoreController $receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * コンポーネントインスタンスを返却する
     * @return object コンポーネントインスタンス
     */
    public function getComponent()
    {
        return $this->component;
    }
}
