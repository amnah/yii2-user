<?php

namespace amnah\yii2\user\models;

/**
 * Userkey module
 *
 * @property string $id
 * @property string $user_id
 * @property integer $type
 * @property string $key
 * @property string $create_time
 * @property string $consume_time
 * @property string $expire_time
 *
 * @property User $user
 */
class Userkey extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return Yii::$app->db->tablePrefix . 'userkey';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'type', 'key'], 'required'],
            [['user_id', 'type'], 'integer'],
            [['create_time', 'consume_time', 'expire_time'], 'safe'],
            [['key'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'key' => 'Key',
            'create_time' => 'Create Time',
            'consume_time' => 'Consume Time',
            'expire_time' => 'Expire Time',
        ];
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
