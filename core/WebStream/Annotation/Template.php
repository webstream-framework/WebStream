<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;

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
    /** template type */
    const TYPE_BASE   = "base";
    const TYPE_SHARED = "shared";
    const TYPE_PARTS  = "parts";

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
        $this->template = $this->annotations["value"];
        if (array_key_exists("name", $this->annotations)) {
            $this->name = $this->annotations["name"];
        }
        $type = "";
        if (array_key_exists("type", $this->annotations)) {
            $type = $this->annotations["type"];
            if ($type === self::TYPE_BASE) {
                $this->isBase = true;
            } elseif ($type === self::TYPE_SHARED) {
                $this->isShared = true;
            } elseif ($type === self::TYPE_PARTS) {
                $this->isParts = true;
            }
        } else {
            $type = "base";
            $this->isBase = true;
        }

        Logger::debug("Template injected: value=" . $this->template . ", type=" . $type);
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
