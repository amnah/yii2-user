<?php

use amnah\yii2\user\models\Profile;
use amnah\yii2\user\models\Role;
use amnah\yii2\user\models\User;
use amnah\yii2\user\models\UserKey;
use yii\db\Schema;

class m140524_153638_init_user extends \yii\db\Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        // create tables. note the specific order
        $this->createTable(Role::tableName(), [
            "id" => Schema::TYPE_PK,
            "name" => Schema::TYPE_STRING . ' not null',
            "create_time" => Schema::TYPE_TIMESTAMP . ' null default null',
            "update_time" => Schema::TYPE_TIMESTAMP . ' null default null',
            "can_admin" => Schema::TYPE_SMALLINT . ' not null default 0',
        ], $tableOptions);
        $this->createTable(User::tableName(), [
            "id" => Schema::TYPE_PK,
            "role_id" => Schema::TYPE_INTEGER . ' not null',
            "status" => Schema::TYPE_SMALLINT . ' not null',
            "email" => Schema::TYPE_STRING . ' null default null',
            "new_email" => Schema::TYPE_STRING . ' null default null',
            "username" => Schema::TYPE_STRING . ' null default null',
            "password" => Schema::TYPE_STRING . ' null default null',
            "auth_key" => Schema::TYPE_STRING . ' null default null',
            "api_key" => Schema::TYPE_STRING . ' null default null',
            "login_ip" => Schema::TYPE_STRING . ' null default null',
            "login_time" => Schema::TYPE_TIMESTAMP . ' null default null',
            "create_ip" => Schema::TYPE_STRING . ' null default null',
            "create_time" => Schema::TYPE_TIMESTAMP . ' null default null',
            "update_time" => Schema::TYPE_TIMESTAMP . ' null default null',
            "ban_time" => Schema::TYPE_TIMESTAMP . ' null default null',
            "ban_reason" => Schema::TYPE_STRING . ' null default null',
        ], $tableOptions);
        $this->createTable(UserKey::tableName(), [
            "id" => Schema::TYPE_PK,
            "user_id" => Schema::TYPE_INTEGER . ' not null',
            "type" => Schema::TYPE_SMALLINT . ' not null',
            "key" => Schema::TYPE_STRING . ' not null',
            "create_time" => Schema::TYPE_TIMESTAMP . ' null default null',
            "consume_time" => Schema::TYPE_TIMESTAMP . ' null default null',
            "expire_time" => Schema::TYPE_TIMESTAMP . ' null default null',
        ], $tableOptions);
        $this->createTable(Profile::tableName(), [
            "id" => Schema::TYPE_PK,
            "user_id" => Schema::TYPE_INTEGER . ' not null',
            "create_time" => Schema::TYPE_TIMESTAMP . ' null default null',
            "update_time" => Schema::TYPE_TIMESTAMP . ' null default null',
            "full_name" => Schema::TYPE_STRING . ' null default null',
        ], $tableOptions);

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
