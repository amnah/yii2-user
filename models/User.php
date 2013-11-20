<?php

namespace amnah\yii2\user\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $role_id
 * @property string $email
 * @property string $username
 * @property string $password
 * @property integer $status
 * @property string $token
 * @property string $ban_time
 * @property string $ban_reason
 * @property string $create_time
 * @property string $update_time
 */
class User extends ActiveRecord implements IdentityInterface {

    /**
     * Id
     * @var int
     */
    public $id;

    /**
     * AuthKey
     * @var string
     */
    public $authKey;

    /**
     * Inactive status
     */
    const STATUS_INACTIVE = 0;

    /**
     * Active status
     */
    const STATUS_ACTIVE = 1;

    /**
     * Unconfirmed status
     */
    const STATUS_UNCONFIRMED = 2;

    /**
     * Banned status
     */
    const STATUS_BANNED = -1;

    /**
     * New password (for registration and changing password)
     * @var string
     */
    public $newPassword;

    /**
     * Captcha code
     * @var string
     */
    public $recaptcha;


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
            [['username'], 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u', 'message' => "{attribute} can contain only letters, numbers, and '_'." ],

            // password rules
            [['password', 'newPassword'], 'length', 'min' => 3],
            [['newPassword'], 'required', 'on' => ['register', 'reset']],

            // recaptcha rules
//            array('recaptcha', 'required', 'on'=> 'register'),
//            array('recaptcha', 'YiiRecaptcha\RecaptchaValidator', 'privateKey' => Yii::app()->params['recaptcha']['private'], 'on'=> 'register'),

//			[['role_id'], 'required'],
//			[['role_id', 'status'], 'integer'],
//			[['ban_time', 'create_time', 'update_time'], 'safe'],
            [['ban_reason'], 'string', 'max' => 255],
		];
	}

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
			'username' => 'Username',
			'password' => 'Password',
			'status' => 'Status',
			'token' => 'Token',
			'ban_time' => 'Ban Time',
			'ban_reason' => 'Ban Reason',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
		];
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
     * Find an identity by the given ID.
     *
     * @param int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id) {
        return static::find($id);
    }

    /**
     * Get id of user
     * @return int current user ID
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get authKey of user
     * @return string
     */
    public function getAuthKey() {
        return $this->authKey;
    }

    /**
     * Validate authKey
     * @param string $authKey
     * @return bool
     */
    public function validateAuthKey($authKey) {
        return $this->authKey === $authKey;
    }

    /**
     * Encrypt newPassword into password
     * @return $this
     */
    public function encryptNewPassword() {
        $this->password = password_hash($this->newPassword, PASSWORD_BCRYPT, array("cost" => 10));
        return $this;
    }

    /**
     * Validate password
     * @param string $password
     * @return bool
     */
    public function validatePassword($password) {
        return password_verify($password, $this->password);
    }
}
