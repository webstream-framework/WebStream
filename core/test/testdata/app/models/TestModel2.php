<?php
/**
 * 事前にテーブルを作成し、データを2件以上作成しておくこと
 * CREATE TABLE users (
 *  id int(0) not null auto_increment,
 *  user_id varchar(32) not null unique,
 *  user_name varchar(128) not null unique
 *  primary key (id)
 * )
 * @Inject
 * @Database("test")
 * @Table("users")
 * @SQL("db/users.properties")
 */
class TestModel2 extends CoreModel2 {
}
