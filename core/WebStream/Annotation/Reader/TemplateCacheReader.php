<?php
namespace WebStream\Annotation\Reader;

use WebStream\Module\Logger;

/**
 * TemplateCacheReader
 * @author Ryuichi TANAKA.
 * @since 2013/10/30
 * @version 0.4
 */
class TemplateCacheReader extends AbstractAnnotationReader
{
    /** 有効期限 */
    private $expire;

    /**
     * {@inheritdoc}
     */
    public function onRead()
    {
        $this->annotation = $this->reader->getAnnotation("WebStream\Annotation\TemplateCache");
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->annotation === null) {
            return;
        }

        $refClass = $this->reader->getReflectionClass();
        $action = $this->reader->getContainer()->router->action();

        $annotationContainerKey = $refClass->getName() . "#" . $action;
        if (!array_key_exists($annotationContainerKey, $this->annotation)) {
            return;
        }

        try {
            $actionContainerList = $this->annotation[$annotationContainerKey];
            foreach ($actionContainerList as $actionContainer) {
                $expire = $actionContainer->expire;
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
                $this->expire = $expire;
            }

        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        }
    }

    /**
     * 有効期限を返却する
     * @return int 有効期限
     */
    public function getExpire()
    {
        return $this->expire;
    }
}
