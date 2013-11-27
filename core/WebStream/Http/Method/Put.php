<?php
namespace WebStream\Http\Method;

use WebStream\Module\Security;

/**
 * Put
 * @author Ryuichi TANAKA.
 * @since 2013/11/21
 * @version 0.4
 */
class Put implements MethodInterface
{
    /** リクエストパラメータ */
    private $params;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        parse_str(file_get_contents('php://input'), $putdata);
        $this->params = Security::safetyIn($putdata);
    }

    /**
     * @Override
     */
    public function params()
    {
        return $this->params;
    }
}
