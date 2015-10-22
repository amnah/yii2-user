<?php

namespace amnah\yii2\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tbl_user_key".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property string $key_value
 * @property string $created_at
 * @property string $updated_at
 * @property string $consumed_at
 * @property string $expired_at
 *
 * @property User $user
 */
class UserKey extends ActiveRecord
{
    /**
     * @var int Key for email activations (for registrations)
     */
    const TYPE_EMAIL_ACTIVATE = 1;

    /**
     * @var int Key for email changes (=updating account page)
     */
    const TYPE_EMAIL_CHANGE = 2;

    /**
     * @var int Key for password resets
     */
    const TYPE_PASSWORD_RESET = 3;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'user_id' => Yii::t('user', 'User ID'),
            'type' => Yii::t('user', 'Type'),
            'key_value' => Yii::t('user', 'Key'),
            'created_at' => Yii::t('user', 'Created At'),
            'updated_at' => Yii::t('user', 'Updated At'),
            'consumed_at' => Yii::t('user', 'Consume Time'),
            'expired_at' => Yii::t('user', 'Expire Time'),
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
                'value' => function () {
                    return date("Y-m-d H:i:s");
                },
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        $user = Yii::$app->getModule("user")->model("User");
        return $this->hasOne($user::className(), ['id' => 'user_id']);
    }

    /**
     * Generate/reuse a userKey
     *
     * @param int $userId
     * @param int $type
     * @param string $expireTime
     * @return static
     */
    public static function generate($userId, $type, $expireTime = null)
    {
        // attempt to find existing record
        // otherwise create new
        $model = static::findActiveByUser($userId, $type);
        if (!$model) {
            $model = new static();
        }

        // set/update data
        $model->user_id = $userId;
        $model->type = $type;
        $model->created_at = date("Y-m-d H:i:s");
        $model->expired_at = $expireTime;
        $model->key_value = Yii::$app->security->generateRandomString();
        $model->save(false);
        return $model;
    }

    /**
     * Find an active userKey by userId
     *
     * @param int $userId
     * @param array|int $type
     * @return static
     */
    public static function findActiveByUser($userId, $type)
    {
        $now = date("Y-m-d H:i:s");
        return static::find()
            ->where([
                "user_id" => $userId,
                "type" => $type,
                "consumed_at" => null,
            ])
            ->andWhere("([[expired_at]] >= '$now' or [[expired_at]] is NULL)")
            ->one();
    }

    /**
     * Find an active userKey by key
     *
     * @param string $key
     * @param array|int $type
     * @return static
     */
    public static function findActiveByKey($key, $type)
    {
        $now = date("Y-m-d H:i:s");
        return static::find()
            ->where([
                "key_value" => $key,
                "type" => $type,
                "consumed_at" => null,
            ])
            ->andWhere("([[expired_at]] >= '$now' or [[expired_at]] is NULL)")
            ->one();
    }

    /**
     * Consume userKey record
     *
     * @return static
     */
    public function consume()
    {
        $this->consumed_at = date("Y-m-d H:i:s");
        $this->save(false);
        return $this;
    }

    /**
     * Expire userKey record
     *
     * @return static
     */
    public function expire()
    {
        $this->expired_at = date("Y-m-d H:i:s");
        $this->save(false);
        return $this;
    }
}