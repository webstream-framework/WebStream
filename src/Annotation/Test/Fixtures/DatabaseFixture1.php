<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Database;

/**
 * @Database(driver="WebStream\Annotation\Test\Fixtures\DatabaseDriverFixture", config="database.config.ini")
 */
class DatabaseFixture1 implements IAnnotatable
{
}
