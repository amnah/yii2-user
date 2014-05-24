<?php

use amnah\yii2\user\models\User;
use amnah\yii2\user\models\UserAuth;

class m140524_153642_init_user_auth extends \yii\db\Migration
{
    public function up()
    {
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
    }

    public function down()
    {
        $this->dropTable(UserAuth::tableName());
    }
}
