<?php
namespace WebStream\Annotation;

use WebStream\Module\Container;

/**
 * FilterComponent
 * @author Ryuichi TANAKA.
 * @since 2013/10/06
 * @version 0.4
 */
class FilterComponent
{
    /** initialize filter container */
    private $initializeContainer;

    /** before filter container */
    private $beforeContainer;

    /** after filter container */
    private $afterContainer;

    /**
     * initialize filterコンテナを設定する
     * @return Container コンテナ
     */
    public function setInitializeContainer(Container $container)
    {
        $this->initializeContainer = $container;
    }

    /**
     * before filterコンテナを設定する
     * @return Container コンテナ
     */
    public function setBeforeContainer(Container $container)
    {
        $this->beforeContainer = $container;
    }

    /**
     * after filterコンテナを設定する
     * @return Container コンテナ
     */
    public function setAfterContainer(Container $container)
    {
        $this->afterContainer = $container;
    }

    /**
     * initialize filterを実行する
     * @return Container コンテナ
     */
    public function initialize()
    {
        $this->execute($this->initializeContainer);
    }

    /**
     * before filterを実行する
     * @return Container コンテナ
     */
    public function before()
    {
        $this->execute($this->beforeContainer);
    }

    /**
     * after filterを実行する
     * @return Container コンテナ
     */
    public function after()
    {
        $this->execute($this->afterContainer);
    }

    /**
     * filterを実行する
     * @return Container コンテナ
     */
    private function execute($containerList)
    {
        for ($i = 0; $i < $containerList->length(); $i++) {
            $containerList->get($i);
        }
    }
}
