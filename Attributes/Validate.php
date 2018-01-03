<?php
namespace WebStream\Annotation\Attributes;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IMethod;
use WebStream\ClassLoader\ClassLoader;
use WebStream\Container\Container;
use WebStream\Exception\Extend\AnnotationException;
use WebStream\Exception\Extend\InvalidRequestException;
use WebStream\Exception\Extend\ValidateException;

/**
 * Validate
 * @author Ryuichi TANAKA.
 * @since 2015/03/30
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class Validate extends Annotation implements IMethod
{
    /**
     * @var array<string> 注入アノテーション情報
     */
    private $injectAnnotation;

    /**
     * {@inheritdoc}
     */
    public function onInject(array $injectAnnotation)
    {
        $this->injectAnnotation = $injectAnnotation;
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(IAnnotatable $instance, \ReflectionMethod $method, Container $container)
    {
        $key = $this->injectAnnotation['key'];
        $rule = $this->injectAnnotation['rule'];
        $method = $this->injectAnnotation['method'];

        if ($method !== null && !array_key_exists($method, array_flip(["get", "post", "put", "delete"]))) {
            throw new ValidateException("Invalid method attribute is specified: " . $method);
        }

        // パラメータの有無にかかわらずルール定義が間違っている場合はエラー
        if (preg_match('/^([a-zA-Z]{1}[a-zA-Z0-9_]{1,})(?:$|\[(.+?)\]$)/', $rule, $matches)) {
            $className = ucfirst(preg_replace_callback('/_([a-zA-Z])/', function ($matches) {
                return ucfirst($matches[1]);
            }, $matches[1]));
            $classpath = null;
            $classLoader = new ClassLoader($container->applicationInfo->applicationRoot);
            $classLoader->inject('logger', $container->logger);

            // デフォルトバリデーションルールのパス
            $filepath = $className . '.php';
            if (!$classLoader->import($filepath)) {
                $loadList = $classLoader->load($className);
                // バリデーションルールのクラス名が複数指定されている場合は適用判断不可能なのでエラー
                if (count($loadList) >= 2) {
                    $errorMsg = "Class load failed because the same class name has been identified: " . $className . "";
                    throw new ValidateException($errorMsg);
                }

                if (count($loadList) === 0) {
                    $errorMsg = "Invalid Validate class filepath: " . $filepath . "";
                    throw new ValidateException($errorMsg);
                }
            }

            $namespaces = $classLoader->getNamespaces($filepath);
            if (count($namespaces) > 0) {
                $classpath = array_shift($namespaces) . "\\" . $className;
            }

            if (!class_exists($classpath)) {
                $errorMsg = "Invalid Validate class's classpath: " . $classpath;
                throw new AnnotationException($errorMsg);
            }

            $validateInstance = new $classpath();
            if ($validateInstance instanceof WebStream\Validate\IValidate) {
                $errorMsg = get_class($validateInstance) . " must be IValidate instance.";
                throw new AnnotationException($errorMsg);
            }

            $params = null;
            if ($container->request->requestMethod === 'GET') {
                if ($method === null || "get" === mb_strtolower($method)) {
                    $params = $container->request->get;
                }
            } elseif ($container->request->requestMethod === 'POST') {
                if ($method === null || "post" === mb_strtolower($method)) {
                    $params = $container->request->post;
                }
            } elseif ($container->request->requestMethod === 'PUT') {
                if ($method === null || "put" === mb_strtolower($method)) {
                    $params = $container->request->put;
                }
            } elseif ($container->request->requestMethod === 'DELETE') {
                if ($method === null || "delete" === mb_strtolower($method)) {
                    $params = $container->request->delete;
                }
            } else {
                $errorMsg = "Unsupported method is specified: " . $method;
                throw new InvalidRequestException($errorMsg);
            }

            // パラメータの指定なしの場合、value=null
            // パラメータの指定ありあつ値の指定なしの場合、value=""
            $value = is_array($params) && array_key_exists($key, $params) ? $params[$key] : null;

            if (!$validateInstance->isValid($value, $rule)) {
                $errorMsg = "Validation rule error. Rule is '$rule', value is " . ($value === null || $value === '' ? "empty" : "'${value}'");
                throw new ValidateException($errorMsg);
            }
        } else {
            $errorMsg = "Invalid validation rule definition: " . $rule;
            throw new ValidateException($errorMsg);
        }
    }
}
