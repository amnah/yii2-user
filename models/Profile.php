<?php

namespace amnah\yii2\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tbl_profile".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string  $create_time
 * @property string  $update_time
 * @property string  $full_name
 *
 * @property User    $user
 */
class Profile extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return static::getDb()->tablePrefix . "profile";
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
//            [['user_id'], 'required'],
//            [['user_id'], 'integer'],
//            [['create_time', 'update_time'], 'safe'],
            [['full_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'          => Yii::t('app', 'ID'),
            'user_id'     => Yii::t('app', 'User ID'),
            'create_time' => Yii::t('app', 'Create Time'),
            'update_time' => Yii::t('app', 'Update Time'),
            'full_name'   => Yii::t('app', 'Full Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        $user = Yii::$app->getModule("user")->model("User");
        return $this->hasOne($user::className(), ['id' => 'user_id']);
    }

    /**
     * Set user id
     *
     * @param int $userId
     * @return static
     */
    public function setUser($userId) {
        $this->user_id = $userId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_time',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update_time',
                ],
                'value' => function() { return date("Y-m-d H:i:s"); },
            ],
        ];
    }

    /**
     * Register a new profile for user
     *
     * @param int $userId
     * @return static
     */
    public function register($userId) {
        $this->user_id = $userId;
        $this->save();
        return $this;
    }
}