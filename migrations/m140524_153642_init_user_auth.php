<?php

use amnah\yii2\user\models\User;
use amnah\yii2\user\models\UserAuth;
use yii\db\Schema;

class m140524_153642_init_user_auth extends \yii\db\Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable(UserAuth::tableName(), [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' not null',
            'provider' => Schema::TYPE_STRING . ' not null',
            'provider_id' => Schema::TYPE_STRING . ' not null',
            'provider_attributes' => Schema::TYPE_TEXT . ' not null',
            'create_time' => Schema::TYPE_TIMESTAMP . ' null default null',
            'update_time' => Schema::TYPE_TIMESTAMP . ' null default null'
        ], $tableOptions);

        // add indexes for performance optimization
        $this->createIndex(UserAuth::tableName() . "_provider_id", UserAuth::tableName(), "provider_id", false);

        // add foreign keys for data integrity
        $this->addForeignKey(UserAuth::tableName() . "_user_id", UserAuth::tableName(), "user_id", User::tableName(), "id");
    }

    public function safeDown()
    {
        $this->dropTable(UserAuth::tableName());
    }
}
