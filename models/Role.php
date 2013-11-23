<?php

namespace amnah\yii2\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Role model
 *
 * @property int $id
 * @property string $name
 * @property string $create_time
 * @property string $update_time
 * @property int $can_admin
 *
 * @property User[] $users
 */
class Role extends ActiveRecord {

    /**
     * @var int Admin user role
     */
    const ADMIN = 1;

    /**
     * @var int Default user role
     */
    const USER = 2;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return Yii::$app->db->tablePrefix . 'role';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['create_time', 'update_time'], 'safe'],
            [['can_admin'], 'boolean'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'can_admin' => 'Can Admin',
        ];
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getUsers() {
        return $this->hasMany(User::className(), ['role_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\AutoTimestamp',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_time', 'update_time'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update_time',
                ],
            ],
        ];
    }
}
