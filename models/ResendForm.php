<?php
/**
 * ResendForm.php
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
 * Forgot password form
 *
 * class p2m\users\models\ResendForm
 */
class ResendForm extends \yii\base\Model
{
	/**
	 * @var string Username and/or email
	 */
	public $email;

	/**
	 * @var \p2m\users\models\User
	 */
	protected $user = false;

	/**
	 * @var \p2m\users\modules\UsersModule
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
	 * @return array the validation rules.
	 */
	public function rules()
	{
		return [
			["email", "required"],
			["email", "email"],
			["email", "validateEmailInactive"],
			["email", "filter", "filter" => "trim"],
		];
	}

	/**
	 * Validate email exists and set user property
	 */
	public function validateEmailInactive()
	{
		// check for valid user
		$user = $this->getUser();
		if (!$user) {
			$this->addError("email", Yii::t("user", "Email not found"));
		} elseif ($user->status == $user::STATUS_ACTIVE) {
			$this->addError("email", Yii::t("user", "Email is already active"));
		} else {
			$this->user = $user;
		}
	}

	/**
	 * Get user based on email
	 * @return \p2m\users\models\User|null
	 */
	public function getUser()
	{
		// get and store user
		if ($this->user === false) {

			/** @var \p2m\users\models\User $user */
			/** @var \p2m\users\models\UserToken $userToken */
			$user = $this->module->model("User");
			$userToken = $this->module->model("UserToken");

			// check email first and then userToken
			$this->user = $user::findOne(["email" => $this->email]);
			if (!$this->user) {
				$userToken = $userToken->findByData($this->email, $userToken::TYPE_EMAIL_CHANGE);
				if ($userToken) {
					$this->user = $user::findOne($userToken->user_id);
				}
			}
		}
		return $this->user;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			"email" => Yii::t("user", "Email"),
		];
	}

	/**
	 * Send forgot email
	 * @return bool
	 */
	public function sendEmail()
	{
		if (!$this->validate()) {
			return false;
		}

		/** @var \p2m\users\models\UserToken $userToken */
		$user = $this->getUser();
		$userToken = $this->module->model("UserToken");

		// calculate type based on user status
		if ($user->status == $user::STATUS_INACTIVE) {
			$type = $userToken::TYPE_EMAIL_ACTIVATE;
		} //elseif ($user->status == $user::STATUS_UNCONFIRMED_EMAIL) {
		else {
			$type = $userToken::TYPE_EMAIL_CHANGE;
		}

		// generate userToken and send email confirmation
		$userToken = $userToken::generate($user->id, $type);
		return $user->sendEmailConfirmation($userToken);
	}
}
?>


