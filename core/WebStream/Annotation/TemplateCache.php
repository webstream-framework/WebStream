<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;

/**
 * TemplateCache
 * @author Ryuichi TANAKA.
 * @since 2013/10/30
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class TemplateCache extends AbstractAnnotation
{
    /** template */
    private $expire;

    /**
     * @Override
     */
    public function onInject()
    {
        if (array_key_exists($this->TEMPLATECACHE_ATTR_EXPIRE, $this->annotations)) {
            $this->expire = $this->annotations[$this->TEMPLATECACHE_ATTR_EXPIRE];
        }
        Logger::debug("TemplateCache enabled.");
    }

    /**
     * 有効期限を返却する
     * @return integer 有効期限
     */
    public function getExpire()
    {
        return $this->expire;
    }
}
