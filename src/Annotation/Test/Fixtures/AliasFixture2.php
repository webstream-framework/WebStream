<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Alias;

/**
 * メソッドエイリアスと同名の実メソッドが定義されている場合
 */
class AliasFixture2 implements IAnnotatable
{
    /**
     * @Alias(name="aliasMethod1")
     */
    public function originMethod1()
    {
    }

    public function aliasMethod1()
    {
    }
}
