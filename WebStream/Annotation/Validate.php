<?php
namespace WebStream\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IMethod;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Container;
use WebStream\Module\Utility\CommonUtils;
use WebStream\Module\Utility\ApplicationUtils;
use WebStream\Module\ClassLoader;
use WebStream\DI\ServiceLocator;
use WebStream\Exception\Extend\ValidateException;
use WebStream\Exception\Extend\AnnotationException;
use WebStream\Exception\Extend\InvalidRequestException;

/**
 * Validate
 * @author Ryuichi TANAKA.
 * @since 2015/03/30
 * @version 0.7
 *
 * @Annotation
 * @Target("METHOD")
 */
class Validate extends Annotation implements IMethod
{
    use CommonUtils, ApplicationUtils;

    /**
     * @var WebStream\Annotation\Container\AnnotationContainer アノテーションコンテナ
     */
    private $annotation;

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
    public function onMethodInject(IAnnotatable &$instance, Container $container, \ReflectionMethod $method)
    {
        $this->injectedLog($this);

        $key = $this->annotation->key;
        $rule = $this->annotation->rule;
        $method = $this->annotation->method;

        $this->injectedContainer->isValid = false;

        if ($method !== null && !$this->inArray($method, ["get", "post", "put", "delete"])) {
            $errorMsg = "Invalid method attribute is specified: " . safetyOut($method);
            throw new ValidateException($errorMsg);
        }

        // パラメータの有無にかかわらずルール定義が間違っている場合はエラー
        if (preg_match('/^([a-zA-Z]{1}[a-zA-Z0-9_]{1,})(?:$|\[(.+?)\]$)/', $rule, $matches)) {
            $className = $this->snake2ucamel($matches[1]);
            $classpath = null;
            $classLoader = new ClassLoader();
            $classLoader->inject('logger', $container->logger)
                        ->inject('applicationInfo', $container->applicationInfo);

            // デフォルトバリデーションルールのパス
            $filepath = $container->applicationInfo->validateRuleDir . $className . ".php";
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

                $namespace = $this->getNamespace($loadList[0]);
                $classpath = $namespace . "\\" . $className;
            }

            $root = $container->applicationInfo->applicationRoot;
            $classpath = $classpath ?: $this->getNamespace($root . "/" . $filepath) . "\\" . $className;

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
                $errorMsg = "Unsupported method is specified: " . safetyOut($method);
                throw new InvalidRequestException($errorMsg);
            }

            // パラメータの指定なしの場合、value=null
            // パラメータの指定ありあつ値の指定なしの場合、value=""
            $value = is_array($params) && array_key_exists($key, $params) ? $params[$key] : null;

            if (!$validateInstance->isValid($value, $rule)) {
                $errorMsg = "Validation rule error. Rule is '$rule', value is " . (safetyOut($value) ?: "null");
                throw new ValidateException($errorMsg);
            }
        } else {
            $errorMsg = "Invalid validation rule definition: " . $rule;
            throw new ValidateException($errorMsg);
        }
    }
}
