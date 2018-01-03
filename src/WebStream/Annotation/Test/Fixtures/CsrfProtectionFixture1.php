<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\CsrfProtection;

/**
 * CSRFチェックできること
 */
class CsrfProtectionFixture1 implements IAnnotatable
{
    /**
     * @CsrfProtection
     */
    public function action()
    {
    }
}

class DummySession
{
    private $dummyToken;

    public function __construct($dummyToken)
    {
        $this->dummyToken = $dummyToken;
    }

    public function get($key)
    {
        return $this->dummyToken;
    }

    public function delete()
    {
    }
}
