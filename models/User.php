<?php

namespace amnah\yii2\user\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property int $role_id
 * @property string $email
 * @property string $new_email
 * @property string $username
 * @property string $password
 * @property int $status
 * @property string $auth_key
 * @property string $create_time
 * @property string $update_time
 * @property string $ban_time
 * @property string $ban_reason
 */
class User extends ActiveRecord implements IdentityInterface {

    /**
     * @var int Inactive status
     */
    const STATUS_INACTIVE = 0;

    /**
     * @var int Active status
     */
    const STATUS_ACTIVE = 1;

    /**
     * @var int Unconfirmed email status
     */
    const STATUS_UNCONFIRMED_EMAIL = 2;

    /**
     * @var int Banned status
     */
    const STATUS_BANNED = 10;

    /**
     * @var string New password - for registration and changing password
     */
    public $newPassword;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return Yii::$app->db->tablePrefix . 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            // general email and username rules
            [['email', 'username'], 'string', 'max' => 255],
            [['email', 'username'], 'unique'],
            [['email', 'username'], 'filter', 'filter' => 'trim'],
            [['email'], 'email'],
            [['username'], 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u', 'message' => "{attribute} can contain only letters, numbers, and '_'."],

            // password rules
            [['password', 'newPassword'], 'length', 'min' => 3],
            [['password', 'newPassword'], 'filter', 'filter' => 'trim'],
            [['newPassword'], 'required', 'on' => ['register', 'reset']],

            // recaptcha rules
//            array('recaptcha', 'required', 'on'=> 'register'),
//            array('recaptcha', 'YiiRecaptcha\RecaptchaValidator', 'privateKey' => Yii::app()->params['recaptcha']['private'], 'on'=> 'register'),

            // admin crud rules
//			[['role_id'], 'required'],
//			[['role_id', 'status'], 'integer'],
//			[['ban_time', 'create_time', 'update_time'], 'safe'],
//			[['ban_reason'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'role_id' => 'Role ID',
            'email' => 'Email',
            'new_email' => 'New Email',
            'username' => 'Username',
            'password' => 'Password',
            'status' => 'Status',
            'auth_key' => 'Auth Key',
            'ban_time' => 'Ban Time',
            'ban_reason' => 'Ban Reason',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',

            // attributes in model
            'newPassword' => ($this->isNewRecord) ? 'Password' : 'New Password',
        ];
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getUserkeys() {
        return $this->hasMany(Userkey::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getProfiles() {
        return $this->hasMany(Profile::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getRole() {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
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

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::find($id);
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->auth_key === $authKey;
    }

    /**
     * Encrypt newPassword into password
     *
     * @return $this
     */
    public function encryptNewPassword() {
        $this->password = password_hash($this->newPassword, PASSWORD_BCRYPT, ["cost" => 10]);

        return $this;
    }

    /**
     * Validate password
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword($password) {
        return password_verify($password, $this->password);
    }
}
