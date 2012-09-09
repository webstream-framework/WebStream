<?php
namespace WebStream;
/**
 * @Inject
 * @Database("test")
 * @Table("users")
 * @Properties("core/test/testdata/sql/users.properties")
 */
class TestModel2 extends CoreModel {
    /**
     * @Inject
     * @SQL("users")
     */
    public function getUserList() {
        return $this->select();
    }
    
    /**
     * @Inject
     * @SQL("users2")
     */
    public function getUserList2($bind) {
        return $this->select($bind);
    }
}

/**
 * @Inject
 * @Database("test2")
 * @Table("users")
 * @Properties("core/test/testdata/sql/users.properties")
 */
class TestModel3 extends CoreModel {}

/**
 * 存在しない@Propertiesが指定された場合
 * @Inject
 * @Database("test2")
 * @Table("users")
 * @Properties("db/users.properties")
 */
class TestModel4 extends CoreModel {}

/**
 * 存在しない@Tableが指定された場合
 * @Inject
 * @Database("test2")
 * @Table("dummy")
 * @Properties("core/test/testdata/sql/users.properties")
 */
class TestModel5 extends CoreModel {}

/**
 * 存在しない@Databaseが指定された場合
 * @Inject
 * @Database("dummy")
 * @Table("test")
 * @Properties("core/test/testdata/sql/users.properties")
 */
class TestModel6 extends CoreModel {}

/**
 * @Inject
 * @Database("test")
 * @Table("users", "users2")
 * @Properties("core/test/testdata/sql/users.properties")
 */
class TestModel7 extends CoreModel {
    /**
     * @Inject
     * @SQL("outer_join")
     */
    public function outerJoin($bind) {
        return $this->select($bind);
    }
}

/**
 * @Inject
 * @Database("test")
 * @Table("users", "users2")
 * @Properties("core/test/testdata/sql/users.properties", "core/test/testdata/sql/users2.properties")
 */
class TestModel8 extends CoreModel {}
