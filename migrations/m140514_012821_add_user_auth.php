<?php

use amnah\yii2\user\models\User;
use amnah\yii2\user\models\UserAuth;

class m140514_012821_add_user_auth extends \yii\db\Migration
{
    /**
     * Up
     *
     * @return bool
     */
    public function up()
    {
        // start transaction
        // note that rollback doesn't undo table creations in mysql
        // @see http://dev.mysql.com/doc/refman/5.1/en/implicit-commit.html
        $transaction = $this->db->beginTransaction();
        try {
            $this->createTable(UserAuth::tableName(), [
                "id" => "int unsigned not null auto_increment primary key",
                "user_id" => "int unsigned not null",
                "provider" => "varchar(255) null not null",
                "provider_id" => "varchar(255) not null",
                "provider_attributes" => "text not null",
                "create_time" => "timestamp null default null",
                "update_time" => "timestamp null default null",
            ]);

            // add indexes for performance optimization
            $this->createIndex(UserAuth::tableName() . "_provider_id", UserAuth::tableName(), "provider_id", false);

            // add foreign keys for data integrity
            $this->addForeignKey(UserAuth::tableName() . "_user_id", UserAuth::tableName(), "user_id", User::tableName(), "id");

            // commit transaction
            $transaction->commit();

        }
        catch (Exception $e) {
            $transaction->rollback();
            echo "Exception: " . $e->getMessage() . "\n";
            return false;
        }

        return true;
    }

    /**
     * Down
     *
     * @return bool
     */
    public function down()
    {
        $transaction = $this->db->beginTransaction();
        try {
            $this->dropTable(UserAuth::tableName());
            $transaction->commit();
        }
        catch (Exception $e) {
            $transaction->rollback();
            echo "Exception: " . $e->getMessage() . "\n";
            return false;
        }

        return true;
    }
}
