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

/**
 * @Inject
 * @Yuruyuri("kyouko", "yui")
 */
class TestAnnotation5 extends TestAnnotation4 {}

/**
 * @Inject
 * @Yuruyuri("akari", "chinachu")
 */
class TestAnnotation6 extends TestAnnotation5 {}


class TestAnnotation7 extends CoreModel2 {
    /**
     * @Inject
     * @Yuri("toshinou")
     */
    public function getKyouko() {}
}

class TestAnnotation8 extends TestAnnotation7 {
    /**
     * @Inject
     * @Yuru("hunami")
     */
    public function getYui() {}
}

class TestAnnotation9 extends TestAnnotation7 {
    /**
     * @Inject
     * @Yuri("sugiura")
     */
    public function getAyano() {}
}

class TestAnnotation10 extends CoreModel2 {
    /**
     * @Inject
     * @Yuruyuri("himawari")
     */
    public function getName() {}
}

class TestAnnotation11 extends TestAnnotation10 {
    /**
     * @Inject
     * @Yuruyuri("sakurako")
     */
    public function getName() {}
}

