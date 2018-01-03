<?php
namespace WebStream\Annotation\Reader\Extend;

use WebStream\DI\Injector;

/**
 * ExtendReader
 * @author Ryuichi TANAKA.
 * @since 2017/01/08
 * @version 0.4
 */
abstract class ExtendReader
{
    use Injector;

    /**
     * @var Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->logger = new class() { function __call($name, $args) {} };
    }

    /**
     * アノテーション情報リストを返却する
     * @param array<object> アノテーション情報リスト
     */
    abstract public function getAnnotationInfo();

    /**
     * read event
     * @param array<string> アノテーション情報リスト
     */
    abstract public function read(array $annotationInfoList);
}
