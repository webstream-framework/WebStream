<?php
/**
 * @Inject
 * @Database("diarysys")
 * @Table("users")
 * @SQL("db/user.properties")
 */
class TestAnnotation1 extends CoreModel2 {}

/**
 * @Database("diarysys")
 * @SQL("db/user.properties")
 */
class TestAnnotation2 extends CoreModel2 {}

class TestAnnotation3 extends CoreModel2 {
    /**
     * @Inject
     * @Database("diarysys")
     */
    public function testAnnotation() {}
}

/**
 * @Inject
 * @Hoge("users", "users2")
 */
class TestAnnotation4 extends CoreModel2 {
    /**
     * @Inject
     * @Fuga("foo", "bar")
     */
    public function testAnnotations() {}
}
