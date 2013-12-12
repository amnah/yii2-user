<?php

use amnah\yii2\user\models\Profile;
use amnah\yii2\user\models\Role;
use amnah\yii2\user\models\User;
use amnah\yii2\user\models\Userkey;

class m131114_141544_add_user extends \yii\db\Migration {

    public function up() {

        // start transaction in case we need to rollback
        $transaction = $this->db->beginTransaction();
        try {
            // create tables in specific order
            $this->createTable(Role::tableName(), [
                "id" => "int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
                "name" => "varchar(255) NOT NULL",
                "create_time" => "timestamp NULL DEFAULT NULL",
                "update_time" => "timestamp NULL DEFAULT NULL",
                "can_admin" => "tinyint NOT NULL DEFAULT 0",
            ]);
            $this->createTable(User::tableName(), [
                "id" => "int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
                "role_id" => "int UNSIGNED NOT NULL",
                "email" => "varchar(255)",
                "new_email" => "varchar(255)",
                "username" => "varchar(255)",
                "password" => "varchar(255)",
                "status" => "tinyint NOT NULL",
                "auth_key" => "varchar(255) NULL DEFAULT NULL",
                "create_time" => "timestamp NULL DEFAULT NULL",
                "update_time" => "timestamp NULL DEFAULT NULL",
                "ban_time" => "timestamp NULL DEFAULT NULL",
                "ban_reason" => "varchar(255) DEFAULT NULL",
            ]);
            $this->createTable(Userkey::tableName(), [
                "id" => "int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
                "user_id" => "int UNSIGNED NOT NULL",
                "type" => "tinyint NOT NULL",
                "key" => "varchar(255) NOT NULL",
                "create_time" => "timestamp NULL DEFAULT NULL",
                "consume_time" => "timestamp NULL DEFAULT NULL",
                "expire_time" => "timestamp NULL DEFAULT NULL",
            ]);
            $this->createTable(Profile::tableName(), [
                "id" => "int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
                "user_id" => "int UNSIGNED NOT NULL",
                "create_time" => "timestamp NULL DEFAULT NULL",
                "update_time" => "timestamp NULL DEFAULT NULL",
                "full_name" => "varchar(255) NOT NULL DEFAULT ''",
            ]);

            // add indices for performance optimization
            $this->createIndex("userkey_key", Userkey::tableName(), "key", true);

            // add foreign keys for data integrity
            $this->addForeignKey("user_role_id", User::tableName(), "role_id", Role::tableName(), "id");
            $this->addForeignKey("profile_user_id", Profile::tableName(), "user_id", User::tableName(), "id");
            $this->addForeignKey("userkey_user_id", Userkey::tableName(), "user_id", User::tableName(), "id");

            // insert role data
            // note: i create a guest role because i like to give guest users the ability to use the site
            //       without registering. you can delete it if you want
            $columns = ["name", "can_admin", "create_time"];
            $this->batchInsert(Role::tableName(), $columns, [
                ["Admin", 1, date("Y-m-d H:i:s")],
                ["User", 0, date("Y-m-d H:i:s")],
                ["Guest", 0, date("Y-m-d H:i:s")],
            ]);

            // insert user data
            $columns = ["id", "role_id", "email", "username", "password", "status", "create_time"];
            $this->batchInsert(User::tableName(), $columns, [
                [1, Role::ROLE_ADMIN, "neo@neo.com", "neo", '$2y$10$WYB666j7MmxuW6b.kFTOde/eGCLijWa6BFSjAAiiRbSAqpC1HCmrC', User::STATUS_ACTIVE, date("Y-m-d H:i:s")],
            ]);

            // insert profile data
            $columns = ["id", "user_id", "full_name", "create_time"];
            $this->batchInsert(Profile::tableName(), $columns, [
                [1, 1, "the one", date("Y-m-d H:i:s")],
            ]);

            // commit transaction
            $transaction->commit();

        }
        catch (Exception $e) {
            echo "Exception: " . $e->getMessage() . "\n";
            $transaction->rollback();

            return false;
        }
    }

    public function down() {

        // get class name for error message
        $class = get_called_class();

        $transaction = $this->db->beginTransaction();
        try {
            // drop tables in specific order - be careful with foreign key constraints
            $this->dropTable(Profile::tableName());
            $this->dropTable(Userkey::tableName());
            $this->dropTable(User::tableName());
            $this->dropTable(Role::tableName());
            $transaction->commit();
        }
        catch (Exception $e) {
            $transaction->rollback();
            echo $e->getMessage() . "\n";
            echo "$class cannot be reverted.\n";

            return false;
        }
    }
}
