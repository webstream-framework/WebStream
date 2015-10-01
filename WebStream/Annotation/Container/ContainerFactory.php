<?php
namespace WebStream\Annotation\Container;

/**
 * ContainerFactory
 * @author Ryuichi TANAKA.
 * @since 2014/05/11
 * @version 0.4
 */
class ContainerFactory
{
    /** アノテーション情報 */
    private $annotation;

    /**
     * constructor
     * @param array<string> アノテーション情報
     */
    public function __construct(array $annotation)
    {
        $this->annotation = $annotation;
    }

    /**
     * アノテーションコンテナを作成する
     * @return Container アノテーションコンテナ
     */
    public function createContainer()
    {
        $container = new AnnotationContainer();
        // 自動的にContainerに詰め込む
        // この時点では安全な値に置換しない
        foreach ($this->annotation as $key => $value) {
            $container->set($key, $value);
        }

        return $container;
    }
}
