<?php
namespace WebStream\Test\DataProvider;

/**
 * SecurityProvider
 * @author Ryuichi TANAKA.
 * @since 2013/09/03
 */
trait SecurityProvider
{
    public function deleteInvisibleCharacterProvider()
    {
        return [['%E3%81%82%00%08%09', '%E3%81%82%09']]; // 00,08は制御文字
    }

    public function replaceXSSStringsProvider()
    {
        return [
            ['<div>\\a\t\n\r\r\n<!-- --><![CDATA[</div>',
             '&lt;div&gt;\\\\a&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/><br/>&lt;!-- --&gt;&lt;![CDATA[&lt;/div&gt;']
        ];
    }
}
