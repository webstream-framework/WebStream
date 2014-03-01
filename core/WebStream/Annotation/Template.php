<?php
namespace WebStream\Annotation;

/**
 * Template
 * @author Ryuichi TANAKA.
 * @since 2013/10/10
 * @version 0.4.1
 *
 * @Annotation
 * @Target("METHOD")
 */
class Template extends AbstractAnnotation
{
    /** template */
    private $template;

    /** variable name */
    private $name;

    /** use base template */
    private $isBase = false;

    /** use shared template */
    private $isShared = false;

    /** use parts template */
    private $isParts = false;

    /**
     * @Override
     */
    public function onInject()
    {
        $this->template = $this->annotations[$this->TEMPLATE_ATTR_VALUE];
        if (array_key_exists($this->TEMPLATE_ATTR_NAME, $this->annotations)) {
            $this->name = $this->annotations[$this->TEMPLATE_ATTR_NAME];
        }
        $type = "";
        if (array_key_exists($this->TEMPLATE_ATTR_TYPE, $this->annotations)) {
            $type = $this->annotations[$this->TEMPLATE_ATTR_TYPE];
            if ($type === $this->TEMPLATE_VALUE_BASE) {
                $this->isBase = true;
            } elseif ($type === $this->TEMPLATE_VALUE_SHARED) {
                $this->isShared = true;
            } elseif ($type === $this->TEMPLATE_VALUE_PARTS) {
                $this->isParts = true;
            }
        } else {
            $type = $this->TEMPLATE_VALUE_BASE;
            $this->isBase = true;
        }
    }

    /**
     * テンプレート名を返却する
     * @return string テンプレート名
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * 変数名を返却する
     * @return string 変数名
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 基本テンプレートを読み込むかどうか
     * @return boolean フラグ
     */
    public function isBase()
    {
        return $this->isBase;
    }

    /**
     * 共通テンプレートから読み込むかどうか
     * @return boolean フラグ
     */
    public function isShared()
    {
        return $this->isShared;
    }

    /**
     * 部分テンプレートとして読み込むかどうか
     * @return boolean フラグ
     */
    public function isParts()
    {
        return $this->isParts;
    }
}
