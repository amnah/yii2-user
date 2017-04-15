<?php
/**
 * User.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @license MIT
 *
 * @package p2made/yii2-p2y2-users
 * @class \p2m\users\models\User
 */

namespace p2m\users\models;

use Yii;
use yii\web\IdentityInterface;
use yii\swiftmailer\Mailer;
use yii\swiftmailer\Message;
use yii\helpers\Inflector;
use ReflectionClass;

use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property integer $role_id
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $access_token
 * @property string $password_reset_token
 * @property integer $status
 * @property string $logged_in_at
 * @property string $logged_in_ip
 * @property string $created_at
 * @property string $created_ip
 * @property string $updated_at
 * @property string $updated_ip
 * @property string $banned_at
 * @property string $banned_reason
 *
 * @property Profile $profile
 * @property Role $role
 * @property UserToken[] $userTokens
 * @property UserAuth[] $userAuths
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
	const STATUS_INACTIVE = 0;
	const STATUS_UNCONFIRMED_EMAIL = 5;
	const STATUS_ACTIVE = 10;
	const STATUS_BANNED = -1;

	/**
	 * @var \amnah\yii2\user\Module
	 */
	public static $module;

	/**
	 * @var string Current password - for account page updates
	 */
	public $currentPassword;

	/**
	 * @var string New password - for registration and changing password
	 */
	public $newPassword;

	/**
	 * @var string New password confirmation - for reset
	 */
	public $newPasswordConfirm;

	/**
	 * @var array Permission cache array
	 */
	protected $permissionCache = [];

	/**
	public static function findByPasswordResetToken($token)
	public static function isPasswordResetTokenValid($token)
	public function setPassword($password)
	public function generateAuthKey()
	public function generatePasswordResetToken()
	public function removePasswordResetToken()
	 */

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		if (!self::$module) {
			self::$module = Yii::$app->getModule("user");
		}
		/*
		if (!$this->module) {
			$this->module = Yii::$app->getModule("user");
		}
		*/
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user}}';
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			TimestampBehavior::className(),
		];
		/*
		return [
			'timestamp' => [
				'class' => 'yii\behaviors\TimestampBehavior',
				'value' => function ($event) {
					return date("Y-m-d H:i:s");
				},
			],
		];
		*/
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		$rules = [
			// general email and username rules
			[['email', 'username'], 'unique'],
			[['email', 'username'], 'filter', 'filter' => 'trim'],
			[['username'], 'string', 'min' => 3, 'max' => 32],
			[['email'], 'email', 'max' => 128],
			[['username'], 'match', 'pattern' => '/^\w+$/u', 'except' => 'social', 'message' => Yii::t('user', '{attribute} can contain only letters, numbers, and "_"')],

			// password rules
			[['newPassword'], 'string', 'min' => 3],
			[['newPassword'], 'filter', 'filter' => 'trim'],
			[['newPassword'], 'required', 'on' => ['register', 'reset']],
			[['newPasswordConfirm'], 'required', 'on' => ['reset']],
			[['newPasswordConfirm'], 'compare', 'compareAttribute' => 'newPassword', 'message' => Yii::t('user', 'Passwords do not match')],

			// account page
			[['currentPassword'], 'validateCurrentPassword', 'on' => ['account']],

			// admin crud rules
			[['role_id', 'status'], 'required', 'on' => ['admin']],
			[['role_id', 'status'], 'integer', 'on' => ['admin']],
			[['banned_at'], 'integer', 'on' => ['admin']],
			[['banned_reason'], 'string', 'max' => 255, 'on' => 'admin'],
		];

		// add required for currentPassword on account page
		// only if $this->password is set (might be null from a social login)
		if ($this->password) {
			$rules[] = [['currentPassword'], 'required', 'on' => ['account']];
		}

		// add required rules for email/username depending on module properties
		if ($this->module->requireEmail) {
			$rules[] = ["email", "required"];
		}
		if ($this->module->requireUsername) {
			$rules[] = ["username", "required"];
		}

		return $rules;

		/*
		return [
			['status', 'default', 'value' => self::STATUS_ACTIVE],
			['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],

			[['role_id', 'status'], 'integer'],
			[['status'], 'required'],
			[['logged_in_at', 'created_at', 'updated_at', 'banned_at'], 'safe'],
			[['email', 'username', 'password', 'auth_key', 'access_token', 'logged_in_ip', 'created_ip', 'banned_reason'], 'string', 'max' => 255],
			[['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['role_id' => 'id']],

			[['role_id', 'email', 'username', 'password', 'auth_key'], 'required'],
			[['role_id', 'status'], 'integer'],
			[['logged_in_at', 'created_at', 'updated_at', 'banned_at'], 'safe'],
			[['email'], 'string', 'max' => 128],
			[['username', 'auth_key'], 'string', 'max' => 32],
			[['password', 'access_token', 'password_reset_token', 'banned_reason'], 'string', 'max' => 255],
			[['logged_in_ip', 'created_ip', 'updated_ip'], 'string', 'max' => 64],
			[['email'], 'unique'],
			[['username'], 'unique'],
			[['email'], 'unique'],
			[['username'], 'unique'],
			[['password_reset_token'], 'unique'],
			[['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['role_id' => 'id']],
		];
		*/
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('user', 'ID'),
			'role_id' => Yii::t('user', 'Role ID'),
			'status' => Yii::t('user', 'Status'),
			'email' => Yii::t('user', 'Email'),
			'username' => Yii::t('user', 'Username'),
			'password' => Yii::t('user', 'Password'),
			'auth_key' => Yii::t('user', 'Auth Key'),
			'access_token' => Yii::t('user', 'Access Token'),
			'logged_in_ip' => Yii::t('user', 'Logged In IP'),
			'logged_in_at' => Yii::t('user', 'Logged In At'),
			'created_ip' => Yii::t('user', 'Created IP'),
			'created_at' => Yii::t('user', 'Created At'),
			'updated_at' => Yii::t('user', 'Updated At'),
			'banned_at' => Yii::t('user', 'Banned At'),
			'banned_reason' => Yii::t('user', 'Banned Reason'),

			// virtual attributes set above
			'currentPassword' => Yii::t('user', 'Current Password'),
			'newPassword' => (
				$this->isNewRecord ?
				Yii::t('user', 'Password') :
				Yii::t('user', 'New Password')
			),
			'newPasswordConfirm' => Yii::t('user', 'New Password Confirm'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentity($id)
	{
		return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentityByAccessToken($token, $type = null)
	{
		return static::findOne(["access_token" => $token]);
	}

	/**
	 * Finds user by username
	 *
	 * @param string $username
	 * @return static|null
	 */
	public static function findByUsername($username)
	{
		return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
	}

	/**
	 * Finds user by password reset token
	 *
	 * @param string $token password reset token
	 * @return static|null
	 */ // // // // //
	public static function findByPasswordResetToken($token)
	{
		if (!static::isPasswordResetTokenValid($token)) {
			return null;
		}

		return static::findOne([
			'password_reset_token' => $token,
			'status' => self::STATUS_ACTIVE,
		]);
	}

	/**
	 * Finds out if password reset token is valid
	 *
	 * @param string $token password reset token
	 * @return bool
	 */ // // // // //
	public static function isPasswordResetTokenValid($token)
	{
		if (empty($token)) {
			return false;
		}

		$timestamp = (int) substr($token, strrpos($token, '_') + 1);
		$expire = Yii::$app->params['user.passwordResetTokenExpire'];
		return $timestamp + $expire >= time();
	}

	/**
	 * @inheritdoc
	 */
	public function getId()
	{
		return $this->id;
		//return $this->getPrimaryKey();
	}

	/**
	 * @inheritdoc
	 */
	public function getAuthKey()
	{
		return $this->auth_key;
	}

	/**
	 * @inheritdoc
	 */
	public function validateAuthKey($authKey)
	{
		return $this->auth_key === $authKey;
	}

	/**
	 * Validates password
	 *
	 * @param string $password password to validate
	 * @return bool if password provided is valid for current user
	 */
	public function validatePassword($password)
	{
		return Yii::$app->security->validatePassword($password, $this->password);
	}

	/**
	 * Generates password hash from password and sets it to the model
	 *
	 * @param string $password
	 */ // // // // //
	public function setPassword($password)
	{
		$this->password_hash = Yii::$app->security->generatePasswordHash($password);
	}

	/**
	 * Generates "remember me" authentication key
	 */ // // // // //
	public function generateAuthKey()
	{
		$this->auth_key = Yii::$app->security->generateRandomString();
	}

	/**
	 * Generates new password reset token
	 */ // // // // //
	public function generatePasswordResetToken()
	{
		$this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
	}

	/**
	 * Removes password reset token
	 */ // // // // //
	public function removePasswordResetToken()
	{
		$this->password_reset_token = null;
	}

	/**
	 * Stick with 1 user:1 profile
	 * @return \yii\db\ActiveQuery
	 */
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProfile()
	{
		$profile = $this->module->model("Profile");
		return $this->hasOne($profile::className(), ['user_id' => 'id']);
		//return $this->hasMany(Profile::className(), ['user_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRole()
	{
		$role = $this->module->model("Role");
		return $this->hasOne($role::className(), ['id' => 'role_id']);
		//return $this->hasOne(Role::className(), ['id' => 'role_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUserAuths()
	{
		$userAuth = $this->module->model("UserAuth");
		return $this->hasMany($userAuth::className(), ['user_id' => 'id']);
		//return $this->hasMany(UserAuth::className(), ['user_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUserTokens()
	{
		$userToken = $this->module->model("UserToken");
		return $this->hasMany($userToken::className(), ['user_id' => 'id']);
		//return $this->hasMany(UserToken::className(), ['user_id' => 'id']);
	}

	/**
	 * @inheritdoc
	 * @return UserQuery the active query used by this AR class.
	 */ // // // // //
	public static function find()
	{
		return new UserQuery(get_called_class());
	}

	/**
	 * Validate current password (account page)
	 */
	public function validateCurrentPassword()
	{
		if (!$this->validatePassword($this->currentPassword)) {
			$this->addError("currentPassword", "Current password incorrect");
		}
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert)
	{
		// check if we're setting $this->password directly
		// handle it by setting $this->newPassword instead
		$dirtyAttributes = $this->getDirtyAttributes();
		if (isset($dirtyAttributes["password"])) {
			$this->newPassword = $dirtyAttributes["password"];
		}

		// hash new password if set
		if ($this->newPassword) {
			$this->password = Yii::$app->security->generatePasswordHash($this->newPassword);
		}

		// convert banned_at checkbox to date
		if ($this->banned_at) {
			$this->banned_at = gmdate("Y-m-d H:i:s");
		}

		// ensure fields are null so they won't get set as empty string
		$nullAttributes = ["email", "username", "banned_at", "banned_reason"];
		foreach ($nullAttributes as $nullAttribute) {
			$this->$nullAttribute = $this->$nullAttribute ? $this->$nullAttribute : null;
		}

		return parent::beforeSave($insert);
	}

	/**
	 * Set attributes for registration
	 * @param int $roleId
	 * @param string $status
	 * @return static
	 */
	public function setRegisterAttributes($roleId, $status = null)
	{
		// set default attributes
		$attributes = [
			"role_id" => $roleId,
			"created_ip" => Yii::$app->request->userIP,
			"auth_key" => Yii::$app->security->generateRandomString(),
			"access_token" => Yii::$app->security->generateRandomString(),
			"status" => static::STATUS_ACTIVE,
		];

		// determine if we need to change status based on module properties
		$emailConfirmation = $this->module->emailConfirmation;
		$requireEmail = $this->module->requireEmail;
		$useEmail = $this->module->useEmail;
		if ($status) {
			$attributes["status"] = $status;
		} elseif ($emailConfirmation && $requireEmail) {
			$attributes["status"] = static::STATUS_INACTIVE;
		} elseif ($emailConfirmation && $useEmail && $this->email) {
			$attributes["status"] = static::STATUS_UNCONFIRMED_EMAIL;
		}

		// set attributes and return
		$this->setAttributes($attributes, false);
		return $this;
	}

	/**
	 * Check for email change
	 * @return string|bool
	 */
	public function checkEmailChange()
	{
		// check if user didn't change email
		if ($this->email == $this->getOldAttribute("email")) {
			return false;
		}

		// check if we need to confirm email change
		if (!$this->module->emailChangeConfirmation) {
			return false;
		}

		// check if user is removing email address (only valid if Module::$requireEmail = false)
		if (!$this->email) {
			return false;
		}

		// update status and email before returning new email
		$newEmail = $this->email;
		$this->status = static::STATUS_UNCONFIRMED_EMAIL;
		$this->email = $this->getOldAttribute("email");
		return $newEmail;
	}

	/**
	 * Update login info (ip and time)
	 * @return bool
	 */
	public function updateLoginMeta()
	{
		$this->logged_in_ip = Yii::$app->request->userIP;
		$this->logged_in_at = gmdate("Y-m-d H:i:s");
		return $this->save(false, ["logged_in_ip", "logged_in_at"]);
	}

	/**
	 * Confirm user email
	 * @param string $newEmail
	 * @return bool
	 */
	public function confirm($newEmail)
	{
		// update status
		$this->status = static::STATUS_ACTIVE;

		// process $newEmail from a userToken
		//   check if another user already has that email
		$success = true;
		if ($newEmail) {
			$checkUser = static::findOne(["email" => $newEmail]);
			if ($checkUser) {
				$success = false;
			} else {
				$this->email = $newEmail;
			}
		}

		$this->save(false, ["email", "status"]);
		return $success;
	}

	/**
	 * Check if user can do specified $permission
	 * @param string $permissionName
	 * @param array $params
	 * @param bool $allowCaching
	 * @return bool
	 */
	public function can($permissionName, $params = [], $allowCaching = true)
	{
		// check for auth manager rbac
		// copied from \yii\web\User
		$auth = Yii::$app->getAuthManager();
		if ($auth) {
			if ($allowCaching && empty($params) && isset($this->permissionCache[$permissionName])) {
				return $this->permissionCache[$permissionName];
			}
			$access = $auth->checkAccess($this->getId(), $permissionName, $params);
			if ($allowCaching && empty($params)) {
				$this->permissionCache[$permissionName] = $access;
			}
			return $access;
		}

		// otherwise use our own custom permission (via the role table)
		return $this->role->checkPermission($permissionName);
	}

	/**
	 * Get display name for the user
	 * @return string|int
	 */
	public function getDisplayName()
	{
		return $this->username ?: $this->email ?: $this->id;
	}

	/**
	 * Send email confirmation to user
	 * @param UserToken $userToken
	 * @return int
	 */
	public function sendEmailConfirmation($userToken)
	{
		/** @var Mailer $mailer */
		/** @var Message $message */

		// modify view path to module views
		$mailer = Yii::$app->mailer;
		$oldViewPath = $mailer->viewPath;
		$mailer->viewPath = $this->module->emailViewPath;

		// send email
		$user = $this;
		$profile = $user->profile;
		$email = $userToken->data ?: $user->email;
		$subject = Yii::$app->id . " - " . Yii::t("user", "Email Confirmation");
		$result = $mailer->compose('confirmEmail', compact("subject", "user", "profile", "userToken"))
			->setTo($email)
			->setSubject($subject)
			->send();

		// restore view path and return result
		$mailer->viewPath = $oldViewPath;
		return $result;
	}

	/**
	 * Get list of statuses for creating dropdowns
	 * @return array
	 */
	public static function statusDropdown()
	{
		// get data if needed
		static $dropdown;
		$constPrefix = "STATUS_";
		if ($dropdown === null) {

			// create a reflection class to get constants
			$reflClass = new ReflectionClass(get_called_class());
			$constants = $reflClass->getConstants();

			// check for status constants (e.g., STATUS_ACTIVE)
			foreach ($constants as $constantName => $constantValue) {

				// add prettified name to dropdown
				if (strpos($constantName, $constPrefix) === 0) {
					$prettyName = str_replace($constPrefix, "", $constantName);
					$prettyName = Inflector::humanize(strtolower($prettyName));
					$dropdown[$constantValue] = $prettyName;
				}
			}
		}

		return $dropdown;
	}
}
