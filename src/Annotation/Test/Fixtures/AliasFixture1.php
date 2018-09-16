<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Alias;

/**
 * 実メソッドにメソッドエイリアスからアクセスできること
 */
class AliasFixture1 implements IAnnotatable
{
    /**
     * @Alias(name="aliasMethod1")
     */
    public function originMethod1()
    {
    }
}
