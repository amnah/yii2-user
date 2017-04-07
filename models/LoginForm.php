<?php
/**
 * LoginForm.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @package p2made/yii2-p2y2-users
 * @license MIT
 */

namespace p2m\users\models;

use Yii;

/**
 * LoginForm is the model behind the login form.
 *
 * class p2m\users\models\LoginForm
 */
class LoginForm extends \yii\base\Model
{
	/**
	 * @var \p2m\users\modules\UsersModule
	 */
	public $module;

	/**
	 * @var string Email and/or username
	 */
	public $email;

	/**
	 * @var string Password
	 */
	public $password;

	/**
	 * @var bool If true, users will be logged in for $loginDuration
	 */
	public $rememberMe = true;

	/**
	 * @var \p2m\users\models\User
	 */
	protected $user = false;

	/**
	 * @return array the validation rules.
	 */
	public function rules()
	{
		return [
			[["email", "password"], "required"],
			["email", "validateUser"],
			["password", "validatePassword"],
			["rememberMe", "boolean"],
		];
	}

	/**
	 * Validate user
	 */
	public function validateUser()
	{
		// check for valid user or if user registered using social auth
		$user = $this->getUser();
		if (!$user || !$user->password) {
			if ($this->module->loginEmail && $this->module->loginUsername) {
				$attribute = "Email / Username";
			} else {
				$attribute = $this->module->loginEmail ? "Email" : "Username";
			}
			$this->addError("email", "$attribute not found");

			// do we need to check $user->userAuths ???
		}

		// check if user is banned
		if ($user && $user->banned_at) {
			$this->addError("email", Yii::t("user", "User is banned - {banReason}", [
				"banReason" => $user->banned_reason,
			]));
		}

		// check status and resend email if inactive
		if ($user && $user->status == $user::STATUS_INACTIVE) {
			/** @var \p2m\users\models\UserToken $userToken */
			$userToken = $this->module->model("UserToken");
			$userToken = $userToken::generate($user->id, $userToken::TYPE_EMAIL_ACTIVATE);
			$user->sendEmailConfirmation($userToken);
			$this->addError("email", Yii::t("user", "Confirmation email resent"));
		}
	}

	/**
	 * Validate password
	 */
	public function validatePassword()
	{
		// skip if there are already errors
		if ($this->hasErrors()) {
			return;
		}

		/** @var \p2m\users\models\User $user */

		// check if password is correct
		$user = $this->getUser();
		if (!$user->validatePassword($this->password)) {
			$this->addError("password", Yii::t("user", "Incorrect password"));
		}
	}

	/**
	 * Get user based on email and/or username
	 * @return \p2m\users\models\User|null
	 */
	public function getUser()
	{
		// check if we need to get user
		if ($this->user === false) {

			// build query based on email and/or username login properties
			$user = $this->module->model("User");
			$user = $user::find();
			if ($this->module->loginEmail) {
				$user->orWhere(["email" => $this->email]);
			}
			if ($this->module->loginUsername) {
				$user->orWhere(["username" => $this->email]);
			}
			$this->user = $user->one();x
		}
		return $this->user;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		// calculate attribute label for "email"
		if ($this->module->loginEmail && $this->module->loginUsername) {
			$attribute = "Email / Username";
		} else {
			$attribute = $this->module->loginEmail ? "Email" : "Username";
		}

		return [
			"email" => Yii::t("user", $attribute),
			"password" => Yii::t("user", "Password"),
			"rememberMe" => Yii::t("user", "Remember Me"),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		if (!$this->module) {
			$this->module = Yii::$app->getModule("user");
		}
	}
}
?>


