<?php

use amnah\yii2\user\models\Profile;
use amnah\yii2\user\models\Role;
use amnah\yii2\user\models\User;
use amnah\yii2\user\models\UserKey;

class m140524_153638_init_user extends \yii\db\Migration
{
    public function safeUp()
    {
        // create tables. note the specific order
        $this->createTable(Role::tableName(), [
            "id" => "int unsigned not null auto_increment primary key",
            "name" => "varchar(255) not null",
            "create_time" => "timestamp null default null",
            "update_time" => "timestamp null default null",
            "can_admin" => "tinyint not null default 0",
        ]);
        $this->createTable(User::tableName(), [
            "id" => "int unsigned not null auto_increment primary key",
            "role_id" => "int unsigned not null",
            "status" => "tinyint not null",
            "email" => "varchar(255) null default null",
            "new_email" => "varchar(255) null default null",
            "username" => "varchar(255) null default null",
            "password" => "varchar(255) null default null",
            "auth_key" => "varchar(255) null default null",
            "api_key" => "varchar(255) null default null",
            "login_ip" => "varchar(45) null default null",
            "login_time" => "timestamp null default null",
            "create_ip" => "varchar(45) null default null",
            "create_time" => "timestamp null default null",
            "update_time" => "timestamp null default null",
            "ban_time" => "timestamp null default null",
            "ban_reason" => "varchar(255) null default null",
        ]);
        $this->createTable(UserKey::tableName(), [
            "id" => "int unsigned not null auto_increment primary key",
            "user_id" => "int unsigned not null",
            "type" => "tinyint not null",
            "key" => "varchar(255) not null",
            "create_time" => "timestamp null default null",
            "consume_time" => "timestamp null default null",
            "expire_time" => "timestamp null default null",
        ]);
        $this->createTable(Profile::tableName(), [
            "id" => "int unsigned not null auto_increment primary key",
            "user_id" => "int unsigned not null",
            "create_time" => "timestamp null default null",
            "update_time" => "timestamp null default null",
            "full_name" => "varchar(255) null default null",
        ]);

        // add indexes for performance optimization
        $this->createIndex(UserKey::tableName() . "_key", UserKey::tableName(), "key", true);
        $this->createIndex(User::tableName() . "_email", User::tableName(), "email", true);
        $this->createIndex(User::tableName() . "_username", User::tableName(), "username", true);

        // add foreign keys for data integrity
        $this->addForeignKey(User::tableName() . "_role_id", User::tableName(), "role_id", Role::tableName(), "id");
        $this->addForeignKey(Profile::tableName() . "_user_id", Profile::tableName(), "user_id", User::tableName(), "id");
        $this->addForeignKey(UserKey::tableName() . "_user_id", UserKey::tableName(), "user_id", User::tableName(), "id");

        // insert role data
        $columns = ["name", "can_admin", "create_time"];
        $this->batchInsert(Role::tableName(), $columns, [
            ["Admin", 1, date("Y-m-d H:i:s")],
            ["User", 0, date("Y-m-d H:i:s")],
        ]);

        // insert admin user: neo/neo
        $security = Yii::$app->security;
        $columns = ["id", "role_id", "email", "username", "password", "status", "create_time", "api_key", "auth_key"];
        $this->batchInsert(User::tableName(), $columns, [
            [1, Role::ROLE_ADMIN, "neo@neo.com", "neo", '$2y$10$WYB666j7MmxuW6b.kFTOde/eGCLijWa6BFSjAAiiRbSAqpC1HCmrC', User::STATUS_ACTIVE, date("Y-m-d H:i:s"), $security->generateRandomString(), $security->generateRandomString()],
        ]);

        // insert profile data
        $columns = ["id", "user_id", "full_name", "create_time"];
        $this->batchInsert(Profile::tableName(), $columns, [
            [1, 1, "the one", date("Y-m-d H:i:s")],
        ]);
    }

    public function safeDown()
    {
        // drop tables in reverse order (for foreign key constraints)
        $this->dropTable(Profile::tableName());
        $this->dropTable(UserKey::tableName());
        $this->dropTable(User::tableName());
        $this->dropTable(Role::tableName());
    }
}
