<?php
namespace WebStream\Annotation;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IMethod;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Container;
use WebStream\Module\Utility\SecurityUtils;
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
    use SecurityUtils;

    /**
     * {@inheritdoc}
     */
    public function onInject(AnnotationContainer $annotation)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(IAnnotatable &$instance, Container $container, \ReflectionMethod $method)
    {
        $this->injectedLog($this);

        $tokenByRequest = $container->request->post($this->getCsrfTokenKey()) ?: $container->request->getHeader($this->getCsrfTokenHeader());
        $tokenInSession = $container->session->get($this->getCsrfTokenKey());
        $container->session->delete($this->getCsrfTokenKey());

        // POSTリクエスト以外はチェックしない
        if (!$container->request->isPost()) {
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
