<?php
namespace WebStream\Annotation\Attributes;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IMethod;
use WebStream\Container\Container;
use WebStream\Exception\Extend\CsrfException;

/**
 * CsrfProtection
 * @author Ryuichi TANAKA.
 * @since 2015/05/08
 * @version 0.7
 *
 * @Annotation
 * @Target("METHOD")
 */
class CsrfProtection extends Annotation implements IMethod
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
     * @var array<string, string> CSRF定数定義
     */
    private $csrfProtectionDefinitions = [
        'tokenKey' => '__CSRF_TOKEN__',
        'tokenHeader' => 'X-CSRF-Token'
    ];

    /**
     * {@inheritdoc}
     */
    public function onInject(array $injectAnnotation)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(IAnnotatable $instance, \ReflectionMethod $method, Container $container)
    {
        $tokenByRequest = null;
        if (array_key_exists($this->csrfProtectionDefinitions['tokenKey'], $container->post)) {
            $tokenByRequest = $container->post[$this->csrfProtectionDefinitions['tokenKey']];
        } elseif (array_key_exists($this->csrfProtectionDefinitions['tokenHeader'], $container->header)) {
            $tokenByRequest = $container->header[$this->csrfProtectionDefinitions['tokenHeader']];
        }

        $tokenInSession = $container->session->get($this->csrfProtectionDefinitions['tokenKey']);
        $container->session->delete($this->csrfProtectionDefinitions['tokenKey']);

        // POSTリクエスト以外はチェックしない
        if ($container->requestMethod !== 'POST') {
            return;
        }

        // リクエストトークン、セッショントークンが両方空はNG
        if ($tokenInSession === null && $tokenByRequest === null) {
            throw new CsrfException("Sent invalid CSRF token");
        }

        // リクエストトークンとセッショントークンが一致しない場合NG
        if ($tokenInSession !== $tokenByRequest) {
            throw new CsrfException("Sent invalid CSRF token");
        }
    }
}
