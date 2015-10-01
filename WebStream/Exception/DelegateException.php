<?php
namespace WebStream\Exception;

/**
 * ApplicationException
 * @author Ryuichi TANAKA.
 * @since 2014/05/05
 * @version 0.4
 */
class DelegateException extends ApplicationException
{
    /**
     * @var bool ハンドリング可否
     */
    private $isHandled;

    /**
     * @var \Exception 例外オブジェクト
     */
    private $originException;

    /**
     * constructor
     * @param \Exception 例外オブジェクト
     */
    public function __construct(\Exception $originException)
    {
        parent::__construct($originException->getMessage(), 500, $originException);
        $this->originException = $originException;
        $this->isHandled = false;
    }

    /**
     * オリジナルの例外を返却する
     * @return \Exception 例外オブジェクト
     */
    public function getOriginException()
    {
        return $this->originException;
    }

    /**
     * ハンドリング可否を返却する
     * @return bool ハンドリング可否
     */
    public function isHandled()
    {
        return $this->isHandled;
    }

    /**
     * ハンドリングを許可する
     */
    public function enableHandled()
    {
        $this->isHandled = true;
    }
}
