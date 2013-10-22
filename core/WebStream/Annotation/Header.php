<?php
namespace WebStream\Annotation;

/**
 * Request
 * @author Ryuichi TANAKA.
 * @since 2013/10/20
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class Header extends AbstractAnnotation
{
    /** contentType */
    private $contentType;

    /** allowMethod */
    private $allowMethod = "GET";

    /**
     * @Override
     */
    public function onInject()
    {
        if (array_key_exists($this->HEADER_ATTR_CONTENTTYPE, $this->annotations)) {
            $this->contentType = $this->annotations[$this->HEADER_ATTR_CONTENTTYPE];
        }
        if (array_key_exists($this->HEADER_ATTR_ALLOWMETHOD, $this->annotations)) {
            $this->allowMethod = $this->annotations[$this->HEADER_ATTR_ALLOWMETHOD];
        }
    }

    /**
     * コンテントタイプを返却する
     * @return string コンテントタイプ
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * 許可されたメソッド名を返却する
     * @return string 許可されたメソッド
     */
    public function getAllowMethod()
    {
        return $this->allowMethod;
    }
}
