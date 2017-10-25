<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Database;

/**
 * @Database(driver="WebStream\Annotation\Test\Fixtures\Undefined", config="database.config.ini")
 */
class DatabaseFixture2 implements IAnnotatable
{
}
