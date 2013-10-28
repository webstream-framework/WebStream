<?php
namespace WebStream\Annotation;

/**
 * TemplateComponent
 * @author Ryuichi TANAKA.
 * @since 2013/10/28
 * @version 0.4
 */
class TemplateComponent
{
    /** ベーステンプレート名 */
    private $base;

    /** 埋め込みテンプレートリスト */
    private $embed;

    /**
     * ベーステンプレート名を設定する
     * @param string ベーステンプレート名
     */
    public function setBase($base)
    {
        $this->base = $base;
    }

    /**
     * ベーステンプレート名を返却する
     * @return string ベーステンプレート名
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * 埋め込みテンプレートを設定する
     * @param array 埋め込みテンプレートリスト
     */
    public function setEmbed(array $embed)
    {
        $this->embed = $embed;
    }

    /**
     * 埋め込みテンプレートリストを返却する
     * @return array 埋め込みテンプレートリスト
     */
    public function getEmbed()
    {
        return $this->embed;
    }
}
