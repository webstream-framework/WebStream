<?php
/**
 * @Inject
 * @Database("diarysys")
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