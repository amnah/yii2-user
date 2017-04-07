<?php
/**
 * ForgotForm.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @package p2made/yii2-p2y2-users
 * @license MIT
 */

namespace p2m\users\models;

use Yii;
use yii\swiftmailer\Mailer;
use yii\swiftmailer\Message;

/**
 * Forgot password form
 *
 * class p2m\users\models\ForgotForm
 */
class ForgotForm extends \yii\base\Model
{
	/**
	 * @var \p2m\users\modules\UsersModule
	 */
	public $module;

	/**
	 * @var string Username and/or email
	 */
	public $email;

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
			["email", "required"],
			["email", "email"],
			["email", "validateEmail"],
			["email", "filter", "filter" => "trim"],
		];
	}

	/**
	 * Validate email exists and set user property
	 */
	public function validateEmail()
	{
		// check for valid user
		$this->user = $this->getUser();
		if (!$this->user) {
			$this->addError("email", Yii::t("user", "Email not found"));
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
			$user = $this->module->model("User");
			$this->user = $user::findOne(["email" => $this->email]);
		}
		return $this->user;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			"email" => "Email",
		];
	}

	/**
	 * Send forgot email
	 * @return bool
	 */
	public function sendForgotEmail()
	{
		/** @var Mailer $mailer */
		/** @var Message $message */
		/** @var \p2m\users\models\UserToken $userToken */

		if ($this->validate()) {

			// get user
			$user = $this->getUser();

			// calculate expireTime
			$expireTime = $this->module->resetExpireTime;
			$expireTime = $expireTime ? date("Y-m-d H:i:s", strtotime($expireTime)) : null;

			// create userToken
			$userToken = $this->module->model("UserToken");
			$userToken = $userToken::generate($user->id, $userToken::TYPE_PASSWORD_RESET, null, $expireTime);

			// modify view path to module views
			$mailer = Yii::$app->mailer;
			$oldViewPath = $mailer->viewPath;
			$mailer->viewPath = $this->module->emailViewPath;

			// send email
			$subject = Yii::$app->id . " - " . Yii::t("user", "Forgot password");
			$result = $mailer->compose('forgotPassword', compact("subject", "user", "userToken"))
				->setTo($user->email)
				->setSubject($subject)
				->send();

			// restore view path and return result
			$mailer->viewPath = $oldViewPath;
			return $result;
		}

		return false;
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
