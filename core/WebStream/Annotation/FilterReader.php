<?php
namespace WebStream\Annotation;

use WebStream\Module\Container;
use WebStream\Exception\AnnotationException;
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
    /** component instance */
    private $component;

    /**
     * @Override
     */
    public function readAnnotation($refClass, $methodName, $arguments)
    {
        $reader = new DoctrineAnnotationReader();
        $component = new FilterComponent();
        $instance = $this->instance;

        $isInitializeDefined = false;
        $initializeContainer = new Container();
        $beforeContainer = new Container();
        $afterContainer = new Container();
        $i = 0;
        $j = 0;

        try {
            while ($refClass !== false) {
                $refMethods = $refClass->getMethods();
                foreach ($refMethods as $refMethod) {
                    if ($refClass->getName() !== $refMethod->class) {
                        continue;
                    }

                    if ($reader->getMethodAnnotation($refMethod, "\WebStream\Annotation\Inject")) {
                        $annotation = $reader->getMethodAnnotation($refMethod, "\WebStream\Annotation\Filter");
                        if ($annotation !== null) {
                            $excepts = null;
                            $onlys = null;
                            if ($annotation->enableInitialize()) {
                                // @Initializeは複数定義許可しない
                                if ($isInitializeDefined) {
                                    throw new AnnotationException("Can not multiple define @Filter(type=\"initialize\") at method.");
                                }
                                $initializeContainer->registerAsLazy(0, function () use ($instance, $refMethod) {
                                    $refMethod->invoke($instance);
                                });
                                $isInitializeDefined = true;
                            }

                            $beforeInfo = $annotation->getBeforeInfo();
                            if ($beforeInfo !== null) {
                                if (array_key_exists("except", $beforeInfo)) {
                                    $excepts = $beforeInfo["except"];
                                }
                                if (array_key_exists("only", $beforeInfo)) {
                                    $onlys = $beforeInfo["only"];
                                }
                                // 両方指定は許可しない
                                if ($excepts !== null && $onlys !== null) {
                                    throw new AnnotationException("Can not defined filter both 'except' and 'only' attribute.");
                                }
                                if ($excepts !== null) {
                                    if (!in_array($methodName, $excepts)) {
                                        $beforeContainer->registerAsLazy($i++, function () use ($instance, $refMethod) {
                                            $refMethod->invoke($instance);
                                        });
                                    }
                                } elseif ($onlys !== null) {
                                    if (in_array($methodName, $onlys)) {
                                        $beforeContainer->registerAsLazy($i++, function () use ($instance, $refMethod) {
                                            $refMethod->invoke($instance);
                                        });
                                    }
                                } else {
                                    $beforeContainer->registerAsLazy($i++, function () use ($instance, $refMethod) {
                                        $refMethod->invoke($instance);
                                    });
                                }
                            }

                            $afterInfo = $annotation->getAfterInfo();
                            if ($afterInfo !== null) {
                                if (array_key_exists("except", $afterInfo)) {
                                    $excepts = $afterInfo["except"];
                                }
                                if (array_key_exists("only", $afterInfo)) {
                                    $onlys = $afterInfo["only"];
                                }
                                // 両方指定は許可しない
                                if ($excepts !== null && $onlys !== null) {
                                    throw new AnnotationException("Can not defined filter both 'except' and 'only' attribute.");
                                }
                                if ($excepts !== null) {
                                    if (!in_array($methodName, $excepts)) {
                                        $afterContainer->registerAsLazy($j++, function () use ($instance, $refMethod) {
                                            $refMethod->invoke($instance);
                                        });
                                    }
                                } elseif ($onlys !== null) {
                                    if (in_array($methodName, $onlys)) {
                                        $afterContainer->registerAsLazy($j++, function () use ($instance, $refMethod) {
                                            $refMethod->invoke($instance);
                                        });
                                    }
                                } else {
                                    $afterContainer->registerAsLazy($j++, function () use ($instance, $refMethod) {
                                        $refMethod->invoke($instance);
                                    });
                                }
                            }
                        }
                    }
                }

                $refClass = $refClass->getParentClass();
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        }

        $component->setInitializeContainer($initializeContainer);
        $component->setBeforeContainer($beforeContainer);
        $component->setAfterContainer($afterContainer);

        $this->component = $component;
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
