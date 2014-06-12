<?php

namespace amnah\yii2\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tbl_user_auth".
 *
 * @property string $id
 * @property string $user_id
 * @property string $provider
 * @property string $provider_id
 * @property string $provider_attributes
 * @property string $create_time
 * @property string $update_time
 *
 * @property User $user
 */
class UserAuth extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return static::getDb()->tablePrefix . "user_auth";
    }

    /**
     * No inputs are used for userAuths
     *
     * @inheritdoc
     */
    /*
    public function rules()
    {
        return [
            [['user_id', 'provider', 'provider_id', 'provider_attributes'], 'required'],
            [['user_id'], 'integer'],
            [['provider_attributes'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['provider_id', 'provider'], 'string', 'max' => 255]
        ];
    }
    */

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                  => Yii::t('user', 'ID'),
            'user_id'             => Yii::t('user', 'User ID'),
            'provider'            => Yii::t('user', 'Provider'),
            'provider_id'         => Yii::t('user', 'Provider ID'),
            'provider_attributes' => Yii::t('user', 'Provider Attributes'),
            'create_time'         => Yii::t('user', 'Create Time'),
            'update_time'         => Yii::t('user', 'Update Time'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => 'yii\behaviors\TimestampBehavior',
                'value'      => function () { return date("Y-m-d H:i:s"); },
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_time',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update_time',
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Set user id
     *
     * @param int $userId
     * @return static
     */
    public function setUser($userId)
    {
        $this->user_id = $userId;
        return $this;
    }

    /**
     * Set provider attributes
     *
     * @param array $attributes
     * @return static
     */
    public function setProviderAttributes($attributes)
    {
        $this->provider_attributes = json_encode($attributes);
        return $this;
    }

    /**
     * Get provider attributes
     *
     * @return array
     */
    public function getProviderAttributes()
    {
        return json_decode($this->provider_attributes, true);
    }
}
