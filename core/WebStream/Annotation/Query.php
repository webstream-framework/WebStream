<?php
namespace WebStream\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Base\IMethods;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Annotation\container\AnnotationListContainer;
use WebStream\Module\Logger;
use WebStream\Module\Container;

/**
 * Query
 * @author Ryuichi TANAKA.
 * @since 2013/10/20
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class Query extends Annotation implements IMethods, IRead
{
    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotaion;

    /**
     * @var AnnotationContainer 注入結果
     */
    private $injectedContainer;

    /**
     * {@inheritdoc}
     */
    public function onInject(AnnotationContainer $annotation)
    {
        $this->annotation = $annotation;
        $this->injectedContainer = new AnnotationContainer();
        Logger::debug("@Query injected.");
    }

    /**
     * {@inheritdoc}
     */
    public function onInjected()
    {
        return $this->injectedContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(CoreInterface &$instance, Container $container, \ReflectionMethod $method)
    {
        $key = $method->class . "#" . $method->name;
        if ($this->injectedContainer->{$key} === null) {
            $this->injectedContainer->{$key} = new AnnotationListContainer();
        }
        $files = $this->annotation->file;
        if (!is_array($files)) {
            $files = [$files];
        }

        $this->injectedContainer->{$key}->pushAsLazy(function () use ($files) {
            $xmlObjectList = [];
            foreach ($files as $file) {
                if (file_exists($file)) {
                    $xmlObjectList[] = simplexml_load_file($file);
                }
            }

            return $xmlObjectList;
        });
    }
}
