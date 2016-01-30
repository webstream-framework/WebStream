<?php
namespace WebStream\Log\Outputter;

/**
 * ConsoleOutputter
 * @author Ryuichi Tanaka
 * @since 2016/01/26
 * @version 0.7
 */
class ConsoleOutputter implements IOutputter
{
    /**
     * https://github.com/php/php-src/tree/master/sapi
     * PHP7以前のものは対応しない
     * @var SAPIリスト
     */
    private $sapis = [
        'apache2handler' => 'http',
        'cgi'            => 'http',
        'cli'            => 'console',
        'fpm'            => 'http',
        'embed'          => 'unsupported',
        'litespeed'      => 'unsupported',
        'phpdbg'         => 'unsupported',
        'tests'          => 'unsupported'
    ];

    /**
     * {@inheritdoc}
     */
    public function write($text)
    {
        $sapi = php_sapi_name();
        if (array_key_exists($sapi, $this->sapis) && $this->sapis[$sapi] === 'console') {
            echo $text . PHP_EOL;
        }
    }
}
