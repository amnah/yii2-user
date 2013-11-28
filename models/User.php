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
 *
 * @property Profile $profile
 * @property Role $role
 * @property Userkey[] $userkeys
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
     * @var string Current password - for account page updates
     */
    public $currentPassword;

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

        // set initial rules
        $rules = [
            // general email and username rules
            [['email', 'username'], 'string', 'max' => 255],
            [['email', 'username'], 'unique'],
            [['email', 'username'], 'filter', 'filter' => 'trim'],
            [['email'], 'email'],
            [['username'], 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u', 'message' => "{attribute} can contain only letters, numbers, and '_'."],

            // password rules
            [['newPassword'], 'string', 'min' => 3],
            [['newPassword'], 'filter', 'filter' => 'trim'],
            [['newPassword'], 'required', 'on' => ['register']],

            // account page
            [['currentPassword'], 'required', 'on' => ['account']],
            [['currentPassword'], "validateCurrentPassword", "on" => ["account"]],

            // admin crud rules
//			[['role_id'], 'required'],
//			[['role_id', 'status'], 'integer'],
//			[['ban_time', 'create_time', 'update_time'], 'safe'],
//			[['ban_reason'], 'string', 'max' => 255],
        ];

        // add required rules for email/username depending on module properties
        $requireFields = ["requireEmail", "requireUsername"];
        foreach ($requireFields as $requireField) {
            if (Yii::$app->getModule("user")->$requireField) {
                $attribute = strtolower(substr($requireField, 7));
                $rules[] = [$attribute, "required"];
            }
        }

        return $rules;
    }

    /**
     * Validate password
     */
    public function validateCurrentPassword() {

        // check password
        if (!$this->verifyPassword($this->currentPassword)) {
            $this->addError("currentPassword", "Current password incorrect");
        }
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
    /*
    public function getProfiles() {
        return $this->hasMany(Profile::className(), ['user_id' => 'id']);
    }
    */

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getProfile() {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
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
                'timestamp' => function() { date("Y-m-d H:i:s"); },
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
     * Get a clean display name for the user
     *
     * @var string $default
     * @return string|int
     */
    public function getDisplayName($default = "") {

        // define possible names
        $possibleNames = [
            "email",
            "username",
            "id",
        ];

        // go through each and return if valid
        foreach ($possibleNames as $possibleName) {
            if (!empty($this->$possibleName)) {
                return $this->$possibleName;
            }
        }

        return $default;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {

        // hash new password if set
        if ($this->newPassword) {
            $this->encryptNewPassword();
        }

        // ensure username and email are null so they won't get set
        $nullAttributes = ["email", "username"];
        foreach ($nullAttributes as $nullAttribute) {
            $this->$nullAttribute = $this->$nullAttribute ? $this->$nullAttribute : null;
        }

        return parent::beforeSave($insert);
    }

    /**
     * Encrypt newPassword into password
     *
     * @return $this
     */
    public function encryptNewPassword() {
        $this->password = password_hash($this->newPassword, PASSWORD_BCRYPT, ["cost" => 12]);

        return $this;
    }

    /**
     * Validate password
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }

    /**
     * Register a new user
     *
     * @param int $roleId
     * @return static
     */
    public function register($roleId) {

        // set default attributes for registration
        $attributes = [ "status" => static::STATUS_ACTIVE, "role_id" => $roleId ];

        // determine if we need to change status based on module properties
        if (Yii::$app->getModule("user")->emailConfirmation and Yii::$app->getModule("user")->requireEmail) {
            $attributes["status"] = User::STATUS_INACTIVE;
        }
        elseif (Yii::$app->getModule("user")->emailConfirmation and Yii::$app->getModule("user")->useEmail) {
            $attributes["status"] = User::STATUS_UNCONFIRMED_EMAIL;
        }

        // set attributes
        $this->setAttributes($attributes, false);

        // save and return
        // note: we assume that we have already validated (both $user and $profile)
        $this->save(false);
        return $this;
    }

    /**
     * Check and prepare for email change
     *
     * @return bool
     */
    public function checkAndPrepareEmailChange() {

        // check for change in email
        if ($this->email != $this->getOldAttribute("email")) {

            // change status
            $this->status = static::STATUS_UNCONFIRMED_EMAIL;

            // set new_email attribute and restore old one
            $this->new_email = $this->email;
            $this->email = $this->getOldAttribute("email");

            return true;
        }

        return false;
    }

    /**
     * Confirm user email
     *
     * @return $this
     */
    public function confirm() {

        // update status
        $this->status = static::STATUS_ACTIVE;

        // update new_email if set
        if ($this->new_email) {
            $this->email = $this->new_email;
            $this->new_email = null;
        }

        // save and return
        $this->save();
        return $this;
    }
}
