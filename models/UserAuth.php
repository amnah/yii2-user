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
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class UserAuth extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'user_id' => Yii::t('user', 'User ID'),
            'provider' => Yii::t('user', 'Provider'),
            'provider_id' => Yii::t('user', 'Provider ID'),
            'provider_attributes' => Yii::t('user', 'Provider Attributes'),
            'created_at' => Yii::t('user', 'Created At'),
            'updated_at' => Yii::t('user', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => function ($event) {
                    return gmdate("Y-m-d H:i:s");
                },
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
     * @return array
     */
    public function getProviderAttributes()
    {
        return json_decode($this->provider_attributes, true);
    }
}
