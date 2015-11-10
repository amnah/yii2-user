<?php

namespace amnah\yii2\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user_token}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property string $token
 * @property string $data
 * @property string $created_at
 * @property string $expired_at
 *
 * @property User $user
 */
class UserToken extends ActiveRecord
{
    /**
     * @var int Token for email activations (for registrations)
     */
    const TYPE_EMAIL_ACTIVATE = 1;

    /**
     * @var int Token for email changes (on /user/account page)
     */
    const TYPE_EMAIL_CHANGE = 2;

    /**
     * @var int Token for password resets
     */
    const TYPE_PASSWORD_RESET = 3;

    /**
     * @var int Token for logging in via email
     */
    const TYPE_EMAIL_LOGIN = 4;

    /**
     * @var \amnah\yii2\user\Module
     */
    public $module;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!$this->module) {
            $this->module = Yii::$app->getModule("user");
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'user_id' => Yii::t('user', 'User ID'),
            'type' => Yii::t('user', 'Type'),
            'token' => Yii::t('user', 'Token'),
            'data' => Yii::t('user', 'Data'),
            'created_at' => Yii::t('user', 'Created At'),
            'expired_at' => Yii::t('user', 'Expired At'),
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
                'updatedAtAttribute' => false,
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
        $user = $this->module->model("User");
        return $this->hasOne($user::className(), ['id' => 'user_id']);
    }

    /**
     * Generate/reuse a userToken
     * @param int $userId
     * @param int $type
     * @param string $data
     * @param string $expireTime
     * @return static
     */
    public static function generate($userId, $type, $data = null, $expireTime = null)
    {
        // attempt to find existing record
        // otherwise create new
        $checkExpiration = false;
        if ($userId) {
            $model = static::findByUser($userId, $type, $checkExpiration);
        } else {
            $model = static::findByData($data, $type, $checkExpiration);
        }
        if (!$model) {
            $model = new static();
        }

        // set/update data
        $model->user_id = $userId;
        $model->type = $type;
        $model->data = $data;
        $model->created_at = gmdate("Y-m-d H:i:s");
        $model->expired_at = $expireTime;
        $model->token = Yii::$app->security->generateRandomString();
        $model->save();
        return $model;
    }

    /**
     * Find a userToken by specified field/value
     * @param string $field
     * @param string $value
     * @param array|int $type
     * @param bool $checkExpiration
     * @return static
     */
    public static function findBy($field, $value, $type, $checkExpiration)
    {
        $query = static::find()->where([$field => $value, "type" => $type ]);
        if ($checkExpiration) {
            $now = gmdate("Y-m-d H:i:s");
            $query->andWhere("([[expired_at]] >= '$now' or [[expired_at]] is NULL)");
        }
        return $query->one();
    }

    /**
     * Find a userToken by userId
     * @param int $userId
     * @param array|int $type
     * @param bool $checkExpiration
     * @return static
     */
    public static function findByUser($userId, $type, $checkExpiration = true)
    {
        return static::findBy("user_id", $userId, $type, $checkExpiration);
    }

    /**
     * Find a userToken by token
     * @param string $token
     * @param array|int $type
     * @param bool $checkExpiration
     * @return static
     */
    public static function findByToken($token, $type, $checkExpiration = true)
    {
        return static::findBy("token", $token, $type, $checkExpiration);
    }

    /**
     * Find a userToken by data
     * @param string $data
     * @param array|int $type
     * @param bool $checkExpiration
     * @return static
     */
    public static function findByData($data, $type, $checkExpiration = true)
    {
        return static::findBy("data", $data, $type, $checkExpiration);
    }
}