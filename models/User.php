<?php

namespace amnah\yii2\user\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\swiftmailer\Mailer;
use yii\helpers\Inflector;
use yii\helpers\Security;
use ReflectionClass;

/**
 * This is the model class for table "tbl_user".
 *
 * @property string    $id
 * @property string    $role_id
 * @property integer   $status
 * @property string    $email
 * @property string    $new_email
 * @property string    $username
 * @property string    $password
 * @property string    $auth_key
 * @property string    $api_key
 * @property string    $login_ip
 * @property string    $login_time
 * @property string    $create_ip
 * @property string    $create_time
 * @property string    $update_time
 * @property string    $ban_time
 * @property string    $ban_reason
 *
 * @property Profile   $profile
 * @property Role      $role
 * @property UserKey[] $userKeys
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
        return static::getDb()->tablePrefix . "user";
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
            [['currentPassword'], 'validateCurrentPassword', 'on' => ['account']],

            // admin crud rules
            [['role_id', 'status'], 'required', 'on' => ['admin']],
            [['role_id', 'status'], 'integer', 'on' => ['admin']],
            [['ban_time'], 'integer', 'on' => ['admin']],
            [['ban_reason'], 'string', 'max' => 255, 'on' => 'admin'],
        ];

        // add required rules for email/username depending on module properties
        $requireFields = ["requireEmail", "requireUsername"];
        foreach ($requireFields as $requireField) {
            if (Yii::$app->getModule("user")->$requireField) {
                $attribute = strtolower(substr($requireField, 7)); // "email" or "username"
                $rules[] = [$attribute, "required"];
            }
        }

        return $rules;
    }

    /**
     * Validate current password (account page)
     */
    public function validateCurrentPassword() {
        if (!$this->verifyPassword($this->currentPassword)) {
            $this->addError("currentPassword", "Current password incorrect");
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'          => Yii::t('app', 'ID'),
            'role_id'     => Yii::t('app', 'Role ID'),
            'status'      => Yii::t('app', 'Status'),
            'email'       => Yii::t('app', 'Email'),
            'new_email'   => Yii::t('app', 'New Email'),
            'username'    => Yii::t('app', 'Username'),
            'password'    => Yii::t('app', 'Password'),
            'auth_key'    => Yii::t('app', 'Auth Key'),
            'api_key'     => Yii::t('app', 'Api Key'),
            'login_ip'    => Yii::t('app', 'Login Ip'),
            'login_time'  => Yii::t('app', 'Login Time'),
            'create_ip'   => Yii::t('app', 'Create Ip'),
            'create_time' => Yii::t('app', 'Create Time'),
            'update_time' => Yii::t('app', 'Update Time'),
            'ban_time'    => Yii::t('app', 'Ban Time'),
            'ban_reason'  => Yii::t('app', 'Ban Reason'),
        ];
    }

    /**
     * Stick with 1 user:1 profile
     * @return \yii\db\ActiveQuery
     */
    /*
    public function getProfiles() {
        return $this->hasMany(Profile::className(), ['user_id' => 'id']);
    }
    */

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile() {
        $profile = Yii::$app->getModule("user")->model("Profile");
        return $this->hasOne($profile::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole() {
        $role = Yii::$app->getModule("user")->model("Role");
        return $this->hasOne($role::className(), ['id' => 'role_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserKeys() {
        $userKey = Yii::$app->getModule("user")->model("UserKey");
        return $this->hasMany($userKey::className(), ['user_id' => 'id']);
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
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token) {
        return static::findOne(["api_key" => $token]);
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
     * Verify password
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password) {
        return Security::validatePassword($password, $this->password);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {

        // hash new password if set
        if ($this->newPassword) {
            $this->password = Security::generatePasswordHash($this->newPassword);
        }

        // convert ban_time checkbox to date
        if ($this->ban_time) {
            $this->ban_time = date("Y-m-d H:i:s");
        }

        // ensure fields are null so they won't get set as empty string
        $nullAttributes = ["email", "username", "ban_time", "ban_reason"];
        foreach ($nullAttributes as $nullAttribute) {
            $this->$nullAttribute = $this->$nullAttribute ? $this->$nullAttribute : null;
        }

        return parent::beforeSave($insert);
    }

    /**
     * Register a new user
     *
     * @param int $roleId
     * @param string $userIp
     * @return static
     */
    public function register($roleId, $userIp) {

        // set default attributes for registration
        $attributes = [
            "role_id" => $roleId,
            "create_ip" => $userIp,
            "auth_key" => Security::generateRandomKey(),
            "api_key" => Security::generateRandomKey(),
        ];

        // determine if we need to change status based on module properties
        $emailConfirmation = Yii::$app->getModule("user")->emailConfirmation;

        // set status inactive if email is required
        if ($emailConfirmation and Yii::$app->getModule("user")->requireEmail) {
            $attributes["status"] = static::STATUS_INACTIVE;
        }
        // set unconfirmed if email is set but NOT required
        elseif ($emailConfirmation and Yii::$app->getModule("user")->useEmail and $this->email) {
            $attributes["status"] = static::STATUS_UNCONFIRMED_EMAIL;
        }
        // set active otherwise
        else {
            $attributes["status"] = static::STATUS_ACTIVE;
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

        // check if user is removing email address
        // this only happens if Module::$requireEmail = false
        if (trim($this->email) === "") {
            return false;
        }

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
     * Set login ip and time
     *
     * @param bool $save Save record
     * @return static
     */
    public function setLoginIpAndTime($save = true) {

        // set data
        $this->login_ip = Yii::$app->getRequest()->getUserIP();
        $this->login_time = date("Y-m-d H:i:s");

        // save and return
        if ($save) {
            $this->save(false, ["login_ip", "login_time"]);
        }
        return $this;
    }

    /**
     * Confirm user email
     *
     * @return static
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
        $this->save(true, ["email", "new_email", "status"]);
        return $this;
    }

    /**
     * Check if user can do specified $permission
     *
     * @param string $permission
     * @return bool
     */
    public function can($permission) {
        return $this->role->checkPermission($permission);
    }

    /**
     * Get display name for the user
     *
     * @var string $default
     * @return string|int
     */
    public function getDisplayName($default = "") {

        // define possible fields
        $possibleNames = [
            "username",
            "email",
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
     * Send email confirmation to user
     *
     * @param UserKey $userKey
     * @return int
     */
    public function sendEmailConfirmation($userKey) {

        // modify view path to module views
        /** @var Mailer $mailer */
        $mailer = Yii::$app->mail;
        $oldViewPath = $mailer->viewPath;
        $mailer->viewPath = Yii::$app->getModule("user")->emailViewPath;

        // send email
        $user = $this;
        $profile = $user->profile;
        $email = $user->new_email !== null ? $user->new_email : $user->email;
        $subject = Yii::$app->id . " - Email confirmation";
        $result = $mailer->compose('confirmEmail', compact("subject", "user", "profile", "userKey"))
            ->setTo($email)
            ->setSubject($subject)
            ->send();

        // restore view path and return result
        $mailer->viewPath = $oldViewPath;
        return $result;
    }

    /**
     * Get list of statuses for creating dropdowns
     *
     * @return array
     */
    public static function statusDropdown() {

        // get data if needed
        static $dropdown;
        if ($dropdown === null) {

            // create a reflection class to get constants
            $refl = new ReflectionClass(get_called_class());
            $constants = $refl->getConstants();

            // check for status constants (e.g., STATUS_ACTIVE)
            foreach ($constants as $constantName => $constantValue) {

                // add prettified name to dropdown
                if (strpos($constantName, "STATUS_") === 0) {
                    $prettyName = str_replace("STATUS_", "", $constantName);
                    $prettyName = Inflector::humanize(strtolower($prettyName));
                    $dropdown[$constantValue] = $prettyName;
                }
            }
        }

        return $dropdown;
    }
}
