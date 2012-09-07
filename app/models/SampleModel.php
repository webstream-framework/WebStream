<?php
/**
 * @Inject
 * @Database("test")
 * @Table("users")
 * @Properties("sql/users.properties")
 */
class SampleModel extends CoreModel2 {
    /**
     * @Inject
     * @SQL("users")
     */
    public function model1() {
        return $this->select();
    }
}
