#!/bin/sh

DIR=$(cd $(dirname $0); pwd)

SQL_CREATE1=$DIR/create_tables1.sql
SQL_CREATE2=$DIR/create_tables2.sql

CMD_MYSQL="mysql -u root -proot"
$CMD_MYSQL < $SQL_CREATE1
$CMD_MYSQL < $SQL_CREATE2
