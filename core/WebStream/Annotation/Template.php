<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;

/**
 * Template
 * @author Ryuichi TANAKA.
 * @since 2013/10/10
 * @version 0.4
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

    /** use shared template */
    private $isShared = false;

    /**
     * @Override
     */
    public function onInject()
    {
        $this->template = $this->annotations["value"];
        if (array_key_exists("name", $this->annotations)) {
            $this->name = $this->annotations["name"];
        }
        if (array_key_exists("type", $this->annotations)) {
            $shared = "_" . $this->annotations["type"];
            if ($shared === STREAM_VIEW_SHARED) {
                $this->isShared = true;
            }
        }

        Logger::debug("Template.");
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
     * _sharedから読み込むかどうか
     * @return boolean フラグ
     */
    public function isShared()
    {
        return $this->isShared;
    }
}
