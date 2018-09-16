<?php
namespace WebStream\Annotation\Attributes;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IMethod;
use WebStream\Annotation\Base\IRead;
use WebStream\Container\Container;
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
     * @var array<string> 注入アノテーション情報
     */
    private $injectAnnotation;

    /**
     * @var array<string> 読み込みアノテーション情報
     */
    private $readAnnotation;

    /**
     * {@inheritdoc}
     */
    public function onInject(array $injectAnnotation)
    {
        $this->injectAnnotation = $injectAnnotation;
        $this->readAnnotation = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAnnotationInfo(): array
    {
        return $this->readAnnotation;
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(IAnnotatable $instance, \ReflectionMethod $method, Container $container)
    {
        $filename = array_key_exists('value', $this->injectAnnotation) ? $this->injectAnnotation['value'] : null;
        $engine = array_key_exists('engine', $this->injectAnnotation) ? $this->injectAnnotation['engine'] : "basic";
        $debug = array_key_exists('debug', $this->injectAnnotation) ? $this->injectAnnotation['debug'] : false;
        $cacheTime = array_key_exists('cacheTime', $this->injectAnnotation) ? $this->injectAnnotation['cacheTime'] : null;
        $logger = $container->logger;

        if (PHP_OS === "WIN32" || PHP_OS === "WINNT") {
            if (preg_match("/^.*[. ]|.*[\p{Cntrl}\/:*?\"<>|].*|(?i:CON|PRN|AUX|CLOCK\$|NUL|COM[1-9]|LPT[1-9])(?:[.].+)?$/", $filename)) {
                throw new AnnotationException("Invalid string contains in @Template('$filename')");
            }
        } else {
            if (preg_match("/:|\.\.\/|\.\.\\\\/", $filename)) {
                throw new AnnotationException("Invalid string contains in @Template('$filename')");
            }
        }

        if ($filename === null) {
            $errorMsg = "Invalid argument of @Template('$filename'). There is no specification of the base template.";
            throw new AnnotationException($errorMsg);
        }

        if ($engine === "twig") {
            if (!is_bool($debug)) {
                if ($debug !== null) {
                    $errorMsg = "Invalid argument of @Template('$filename'). 'debug' attribute bool only be specified.";
                    throw new AnnotationException($errorMsg);
                }
                $debug = false;
            }
            $this->readAnnotation['filename'] = $filename;
            $this->readAnnotation['engine'] = $container->engine['twig'];
            $this->readAnnotation['debug'] = $debug;

            if ($cacheTime !== null) {
                $logger->warn("'cacheTime' attribute is not used in Twig template.");
            }
        } elseif ($engine === "basic") {
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
                    $logger->warn("Expire value converted the maximum of PHP Integer.");
                }

                $this->readAnnotation['cacheTime'] = $cacheTime;
            }

            $this->readAnnotation['filename'] = $filename;
            $this->readAnnotation['engine'] = $container->engine['basic'];

            if ($debug !== null) {
                $logger->warn("'debug' attribute is not used in Basic template.");
            }
        } else {
            $errorMsg = "Invalid 'engine' attribute of @Template('$filename').";
            throw new AnnotationException($errorMsg);
        }
    }
}
