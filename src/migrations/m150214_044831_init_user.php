<?php

namespace faro\core\user\migrations;

use Yii;
use yii\db\Schema;
use yii\db\Migration;

class m150214_044831_init_user extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // create tables. note the specific order
        $this->createTable('{{%core_acl_rol}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' not null',
            'created_at' => Schema::TYPE_TIMESTAMP . ' null',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' null',
            'can_admin' => Schema::TYPE_SMALLINT . ' not null default 0',
        ], $tableOptions);
        $this->createTable('{{%core_acl_usuario}}', [
            'id' => Schema::TYPE_PK,
            'role_id' => Schema::TYPE_INTEGER . ' not null',
            'status' => Schema::TYPE_SMALLINT . ' not null',
            'email' => Schema::TYPE_STRING . ' null',
            'username' => Schema::TYPE_STRING . ' null',
            'password' => Schema::TYPE_STRING . ' null',
            'auth_key' => Schema::TYPE_STRING . ' null',
            'access_token' => Schema::TYPE_STRING . ' null',
            'logged_in_ip' => Schema::TYPE_STRING . ' null',
            'logged_in_at' => Schema::TYPE_TIMESTAMP . ' null',
            'created_ip' => Schema::TYPE_STRING . ' null',
            'created_at' => Schema::TYPE_TIMESTAMP . ' null',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' null',
            'banned_at' => Schema::TYPE_TIMESTAMP . ' null',
            'banned_reason' => Schema::TYPE_STRING . ' null',
        ], $tableOptions);
        $this->createTable('{{%core_acl_usuario_token}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' null',
            'type' => Schema::TYPE_SMALLINT . ' not null',
            'token' => Schema::TYPE_STRING . ' not null',
            'data' => Schema::TYPE_STRING . ' null',
            'created_at' => Schema::TYPE_TIMESTAMP . ' null',
            'expired_at' => Schema::TYPE_TIMESTAMP . ' null',
        ], $tableOptions);
        $this->createTable('{{%core_acl_perfil}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' not null',
            'created_at' => Schema::TYPE_TIMESTAMP . ' null',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' null',
            'full_name' => Schema::TYPE_STRING . ' null',
            'timezone' => Schema::TYPE_STRING . ' null',
        ], $tableOptions);
        $this->createTable('{{%core_acl_usuario_auth}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' not null',
            'provider' => Schema::TYPE_STRING . ' not null',
            'provider_id' => Schema::TYPE_STRING . ' not null',
            'provider_attributes' => Schema::TYPE_TEXT . ' not null',
            'created_at' => Schema::TYPE_TIMESTAMP . ' null',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' null'
        ], $tableOptions);

        // add indexes for performance optimization
        $this->createIndex('{{%core_acl_usuario_email}}', '{{%core_acl_usuario}}', 'email', true);
        $this->createIndex('{{%core_acl_usuario_username}}', '{{%core_acl_usuario}}', 'username', true);
        $this->createIndex('{{%core_acl_usuario_token_token}}', '{{%core_acl_usuario_token}}', 'token', true);
        $this->createIndex('{{%core_acl_usuario_auth_provider_id}}', '{{%core_acl_usuario_auth}}', 'provider_id', false);

        // add foreign keys for data integrity
        $this->addForeignKey('{{%core_acl_usuario_role_id}}', '{{%core_acl_usuario}}', 'role_id', '{{%core_acl_rol}}', 'id');
        $this->addForeignKey('{{%core_acl_perfil_user_id}}', '{{%core_acl_perfil}}', 'user_id', '{{%core_acl_usuario}}', 'id');
        $this->addForeignKey('{{%core_acl_usuario_token_user_id}}', '{{%core_acl_usuario_token}}', 'user_id', '{{%core_acl_usuario}}', 'id');
        $this->addForeignKey('{{%core_acl_usuario_auth_user_id}}', '{{%core_acl_usuario_auth}}', 'user_id', '{{%core_acl_usuario}}', 'id');

        // insert role data
        $columns = ['name', 'can_admin', 'created_at'];
        $this->batchInsert('{{%core_acl_rol}}', $columns, [
            ['Admin', 1, gmdate('Y-m-d H:i:s')],
            ['User', 0, gmdate('Y-m-d H:i:s')],
        ]);

        // insert admin user: neo/neo
        $security = Yii::$app->security;
        $columns = ['role_id', 'email', 'username', 'password', 'status', 'created_at', 'access_token', 'auth_key'];
        $this->batchInsert('{{%core_acl_usuario}}', $columns, [
            [
                1, // Role::ROLE_ADMIN
                'admin@faro.works',
                'admin',
                '$2y$13$dyVw4WkZGkABf2UrGWrhHO4ZmVBv.K4puhOL59Y9jQhIdj63TlV.O', // neo
                1, // User::STATUS_ACTIVE
                gmdate('Y-m-d H:i:s'),
                $security->generateRandomString(),
                $security->generateRandomString(),
            ],
        ]);

        // insert profile data
        $columns = ['user_id', 'full_name', 'created_at'];
        $this->batchInsert('{{%core_acl_perfil}}', $columns, [
            [1, 'the one', gmdate('Y-m-d H:i:s')],
        ]);
    }

    public function down()
    {
        // drop tables in reverse order (for foreign key constraints)
        $this->dropTable('{{%core_acl_usuario_auth}}');
        $this->dropTable('{{%core_acl_perfil}}');
        $this->dropTable('{{%core_acl_usuario_token}}');
        $this->dropTable('{{%core_acl_usuario}}');
        $this->dropTable('{{%core_acl_rol}}');
    }
}
