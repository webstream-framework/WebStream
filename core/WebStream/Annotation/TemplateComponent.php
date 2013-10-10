<?php
namespace WebStream\Annotation;

/**
 * TemplateComponent
 * @author Ryuichi TANAKA.
 * @since 2013/10/10
 * @version 0.4
 */
class TemplateComponent
{
    /** テンプレート名 */
    private $template;

    /** 埋め込み用テンプレートマップリスト */
    private $embedTemplates = [];

    /**
     * テンプレートファイル名を設定する
     * @param string テンプレートファイル名
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * テンプレートファイル名を設定する
     * @param string テンプレートエイリアス
     * @param string テンプレートファイル名
     */
    public function setEmbedTemplate($alias, $template)
    {
        $this->embedTemplates[] = [$alias => $template];
    }

    /**
     * テンプレートファイル名を返却する
     * @return string テンプレートファイル名
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * 埋め込み用テンプレートマップリストを返却する
     * @return array テンプレートマップリスト
     */
    public function getEmbedTemplates()
    {
        return $this->embedTemplates;
    }
}
