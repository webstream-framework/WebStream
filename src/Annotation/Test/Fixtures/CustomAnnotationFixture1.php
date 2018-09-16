<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;

class CustomAnnotationFixture1 implements IAnnotatable
{
    /**
     * @CustomAnnotation(name="test")
     */
    public function action()
    {
    }
}
