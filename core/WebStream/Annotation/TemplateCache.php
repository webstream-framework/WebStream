<?php
namespace WebStream\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Base\IMethod;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Logger;
use WebStream\Module\Container;
use WebStream\Exception\Extend\AnnotationException;

/**
 * TemplateCache
 * @author Ryuichi TANAKA.
 * @since 2013/10/20
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class TemplateCache extends Annotation implements IMethod, IRead
{
    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotaion;

    /**
     * {@inheritdoc}
     */
    public function onInject(AnnotationContainer $annotation)
    {
        $this->annotation = $annotation;
        Logger::debug("@TemplateCache injected.");
    }

    /**
     * {@inheritdoc}
     */
    public function onInjected()
    {
        return $this->annotation;
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(CoreInterface &$instance, Container $container, \ReflectionMethod $method)
    {
        $expire = $this->annotation->expire;

        // 複数指定は不可
        if (is_array($expire)) {
            $errorMsg = "Invalid argument of @TemplateCache attribute 'expire' should not be array.";
            throw new AnnotationException($errorMsg);
        }
        // 数値以外は不可
        if (!preg_match("/^[1-9]{1}[0-9]{0,}$/", $expire)) {
            $errorMsg = "Invalid argument of @TemplateCache attribute 'expire' should not be integer.";
            throw new AnnotationException($errorMsg);
        }

        $expire = intval($expire);
        if ($expire <= 0) {
            $errorMsg = "Expire value is out of integer range: @TemplateCache(expire=" . strval($expire) . ")";
            throw new AnnotationException($errorMsg);
        } elseif ($expire >= PHP_INT_MAX) {
            Logger::warn("Expire value converted the maximum of PHP Integer.");
        }
    }
}
