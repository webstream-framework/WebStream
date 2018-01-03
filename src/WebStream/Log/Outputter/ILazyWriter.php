<?php
namespace WebStream\Log\Outputter;

/**
 * ILazyWriter
 * @author Ryuichi TANAKA.
 * @since 2015/01/30
 * @version 0.7
 */
interface ILazyWriter
{
    /**
     * 遅延書き出しを有効にする
     */
    public function enableLazyWrite();

    /**
     * 即時書き出しを有効にする
     */
    public function enableDirectWrite();
}
