<?php
namespace WebStream\Log\Outputter;

/**
 * IOutputter
 * @author Ryuichi TANAKA.
 * @since 2015/01/26
 * @version 0.7
 */
interface IOutputter
{
    /**
     * ログを出力する
     * @param string $message ログメッセージ
     */
    public function write($message);
}
