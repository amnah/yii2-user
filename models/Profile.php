<?php

namespace amnah\yii2\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Profile model
 *
 * @property int $id
 * @property int $user_id
 * @property string $create_time
 * @property string $update_time
 * @property string $first_name
 * @property string $last_name
 *
 * @property User $user
 */
class Profile extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return Yii::$app->db->tablePrefix . 'profile';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['first_name', 'last_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
        ];
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
