-- テストテーブル(mysql)
drop table if exists stream_test;
create table stream_test (
    id int auto_increment primary key,
    title varchar(255) not null unique
);
