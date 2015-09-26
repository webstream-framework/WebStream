<?php
namespace WebStream\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IMethod;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Logger;
use WebStream\Module\Container;
use WebStream\Module\Utility;
use WebStream\Module\ClassLoader;
use WebStream\Exception\Extend\ValidateException;
use WebStream\Exception\Extend\AnnotationException;

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
    use Utility;

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
        $this->injectedContainer = new AnnotationContainer();
        Logger::debug("@Validate injected.");
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(CoreInterface &$instance, Container $container, \ReflectionMethod $method)
    {
        $key = $this->annotation->key;
        $rule = $this->annotation->rule;
        $method = $this->annotation->method;

        $this->injectedContainer->isValid = false;

        if ($method !== null && !$this->inArray($method, ["get", "post", "put", "delete"])) {
            $errorMsg = "Invalid method attribute is specified: " . safetyOut($method);
            throw new AnnotationException($errorMsg);
        }

        // パラメータの有無にかかわらずルール定義が間違っている場合はエラー
        if (preg_match('/^([a-zA-Z]{1}[a-zA-Z0-9_]{1,})(?:$|\[(.+?)\]$)/', $rule, $matches)) {
            $className = $this->snake2ucamel($matches[1]);
            $classpath = null;
            $classLoader = new ClassLoader();
            // デフォルトバリデーションルールのパス
            $filepath = "core/WebStream/Validate/Rule/" . $className . ".php";
            if (!$classLoader->import($filepath)) {
                $loadList = $classLoader->load($className);
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

            $classpath = $classpath ?: $this->getNamespace($this->getRoot() . "/" . $filepath) . "\\" . $className;
            if (!class_exists($classpath)) {
                $errorMsg = "Invalid Validate class's classpath: " . $classpath . "";
                throw new AnnotationException($errorMsg);
            }

            $validateInstance = new $classpath();
            if ($validateInstance instanceof WebStream\Validate\IValidate) {
                $errorMsg = get_class($validateInstance) . " must be IValidate instance.";
                throw new AnnotationException($errorMsg);
            }

            $params = null;
            if ($container->request->isGet()) {
                if ($method === null || "get" === mb_strtolower($method)) {
                    $params = $container->request->get();
                }
            } elseif ($container->request->isPost()) {
                if ($method === null || "post" === mb_strtolower($method)) {
                    $params = $container->request->post();
                }
            } elseif ($container->request->isPut()) {
                if ($method === null || "put" === mb_strtolower($method)) {
                    $params = $container->request->put();
                }
            } elseif ($container->request->isDelete()) {
                if ($method === null || "delete" === mb_strtolower($method)) {
                    $params = $container->request->delete();
                }
            } else {
                $errorMsg = "Unsupported method is specified: " . safetyOut($method);
                throw new AnnotationException($errorMsg);
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
