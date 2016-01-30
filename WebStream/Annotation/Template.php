<?php
namespace WebStream\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IMethod;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Container;
use WebStream\Template\Basic;
use WebStream\Template\Twig;
use WebStream\Exception\Extend\AnnotationException;

/**
 * Template
 * @author Ryuichi TANAKA.
 * @since 2013/10/10
 * @version 0.7
 *
 * @Annotation
 * @Target("METHOD")
 */
class Template extends Annotation implements IMethod, IRead
{
    /**
     * @var WebStream\Annotation\Container\AnnotationContainer アノテーションコンテナ
     */
    private $annotation;

    /**
     * @var WebStream\Annotation\Container\AnnotationContainer 注入結果
     */
    private $injectedContainer;

    /**
     * {@inheritdoc}
     */
    public function onInject(AnnotationContainer $annotation)
    {
        $this->annotation = $annotation;
        $this->injectedContainer = new AnnotationContainer();
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
    public function onMethodInject(IAnnotatable &$instance, Container $container, \ReflectionMethod $method)
    {
        $this->injectedLog($this);

        $filename = $this->annotation->value;
        $engine = $this->annotation->engine ?: "basic";
        $debug = $this->annotation->debug;
        $cacheTime = $this->annotation->cacheTime;

        if (PHP_OS === "WIN32" || PHP_OS === "WINNT") {
            if (preg_match("/^.*[. ]|.*[\p{Cntrl}\/:*?\"<>|].*|(?i:CON|PRN|AUX|CLOCK\$|NUL|COM[1-9]|LPT[1-9])(?:[.].+)?$/", $filename)) {
                throw new AnnotationException("Invalid string contains in @Template('" . safetyOut($filename) . "')");
            }
        } else {
            if (preg_match("/:|\.\.\/|\.\.\\\\/", $filename)) {
                throw new AnnotationException("Invalid string contains in @Template('" . safetyOut($filename) . "')");
            }
        }

        if ($filename === null) {
            $errorMsg = "Invalid argument of @Template('" . safetyOut($filename) . "'). ";
            $errorMsg.= "There is no specification of the base template.";
            throw new AnnotationException($errorMsg);
        }

        $container->filename = $filename;

        if ($engine === "twig") {
            if (!is_bool($debug)) {
                if ($debug !== null) {
                    $errorMsg = "Invalid argument of @Template('" . safetyOut($filename) . "'). ";
                    $errorMsg.= "'debug' attribute bool only be specified.";
                    throw new AnnotationException($errorMsg);
                }
                $debug = false;
            }
            $container->debug = $debug;

            if ($cacheTime !== null) {
                $this->logger->warn("'cacheTime' attribute is not used in Twig template.");
            }

            $this->injectedContainer->engine = new Twig($container);
        } elseif ($engine === "basic") {
            if ($debug !== null) {
                $this->logger->warn("'debug' attribute is not used in Basic template.");
            }

            if ($cacheTime !== null) {
                // 複数指定は不可
                if (is_array($cacheTime)) {
                    $errorMsg = "Invalid argument of @Template attribute 'cacheTime' should not be array.";
                    throw new AnnotationException($errorMsg);
                }
                // 数値以外は不可
                if (!preg_match("/^[1-9]{1}[0-9]{0,}$/", $cacheTime)) {
                    $errorMsg = "Invalid argument of @Template attribute 'cacheTime' should not be integer.";
                    throw new AnnotationException($errorMsg);
                }

                $cacheTime = intval($cacheTime);
                if ($cacheTime <= 0) {
                    $errorMsg = "Expire value is out of integer range: @Template(cacheTime=" . strval($cacheTime) . ")";
                    throw new AnnotationException($errorMsg);
                } elseif ($cacheTime >= PHP_INT_MAX) {
                    $this->logger->warn("Expire value converted the maximum of PHP Integer.");
                }

                $this->injectedContainer->cacheTime = $cacheTime;
            }

            $this->injectedContainer->engine = new Basic($container);
        } else {
            $errorMsg = "Invalid 'engine' attribute of @Template('" . safetyOut($filename) . "'.";
            throw new AnnotationException($errorMsg);
        }
    }
}
