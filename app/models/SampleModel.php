<?php
namespace WebStream;
/**
 * @Inject
 * @Database("test")
 * @Table("users")
 * @Properties("sql/users.properties")
 */
class SampleModel extends CoreModel {
    /**
     * @Inject
     * @SQL("users")
     */
    public function model1() {
        return $this->select();
    }
}
