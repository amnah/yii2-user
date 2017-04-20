<?php
/**
 * DefaultController.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @license MIT
 *
 * @package p2made/yii2-p2y2-users
 * @class \p2m\users\controllers\DefaultController
 */

namespace p2m\users\controllers;

use Yii;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;

/**
 * Default controller for User module
 */
class DefaultController extends \yii\web\Controller
{
	/**
	 * @var \p2m\users\modules\UsersModule
	 * @inheritdoc
	 */
	public $module;

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'actions' => ['index', 'confirm', 'resend', 'logout'],
						'allow' => true,
						'roles' => ['?', '@'],
					],
					[
						'actions' => ['account', 'profile', 'resend-change', 'cancel'],
						'allow' => true,
						'roles' => ['@'],
					],
					[
						'actions' => ['login', 'register', 'forgot', 'reset', 'login-email', 'login-callback'],
						'allow' => true,
						'roles' => ['?'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	/**
	 * Display index - debug page, login page, or account page
	 */
	public function actionIndex()
	{
		if (defined('YII_DEBUG') && YII_DEBUG) {
			$actions = $this->module->getActions();
			return $this->render('index', ["actions" => $actions]);
		} elseif (Yii::$app->user->isGuest) {
			return $this->redirect(["/user/login"]);
		} else {
			return $this->redirect(["/user/account"]);
		}
	}

	/**
	 * Display login page
	 */
	public function actionLogin()
	{
		/** @var \p2m\users\models\LoginForm $model */
		$model = $this->module->model("LoginForm");

		// load post data and login
		$post = Yii::$app->request->post();
		if ($model->load($post) && $model->validate()) {
			$returnUrl = $this->performLogin($model->getUser(), $model->rememberMe);
			return $this->redirect($returnUrl);
		}

		return $this->render('login', compact("model"));
	}

	/**
	 * Login/register via email
	 */
	public function actionLoginEmail()
	{
		/** @var \p2m\users\models\LoginEmailForm $loginEmailForm */
		$loginEmailForm = $this->module->model("LoginEmailForm");

		// load post data and validate
		$post = Yii::$app->request->post();
		if ($loginEmailForm->load($post) && $loginEmailForm->sendEmail()) {
			$user = $loginEmailForm->getUser();
			$message = $user ? "Login link sent" : "Registration link sent";
			$message .= " - Please check your email";
			Yii::$app->session->setFlash("Login-success", Yii::t("user", $message));
		}

		return $this->render("loginEmail", compact("loginEmailForm"));
	}

	/**
	 * Login/register callback via email
	 */
	public function actionLoginCallback($token)
	{
		/** @var \p2m\users\models\User $user */
		/** @var \p2m\users\models\Profile $profile */
		/** @var \p2m\users\models\Role $role */
		/** @var \p2m\users\models\UserToken $userToken */

		$user = $this->module->model("User");
		$profile = $this->module->model("Profile");
		$userToken = $this->module->model("UserToken");

		// check token and log user in directly
		$userToken = $userToken::findByToken($token, $userToken::TYPE_EMAIL_LOGIN);
		if ($userToken && $userToken->user) {
			$returnUrl = $this->performLogin($userToken->user, $userToken->data);
			$userToken->delete();
			return $this->redirect($returnUrl);
		}

		// load post data
		$post = Yii::$app->request->post();
		$userLoaded = $user->load($post);
		$profileLoaded = $profile->load($post);
		if ($userToken && ($userLoaded || $profileLoaded)) {

			// ensure that email is taken from the $userToken (and not from user input)
			$user->email = $userToken->data;

			// validate and register
			if ($user->validate() && $profile->validate()) {
				$role = $this->module->model("Role");
				$user->setRegisterAttributes($role::ROLE_USER, $user::STATUS_ACTIVE)->save();
				$profile->setUser($user->id)->save();

				// log user in and delete token
				$returnUrl = $this->performLogin($user);
				$userToken->delete();
				return $this->redirect($returnUrl);
			}
		}

		$user->email = $userToken ? $userToken->data : null;
		return $this->render("loginCallback", compact("user", "profile", "userToken"));
	}

	/**
	 * Perform the login
	 */
	protected function performLogin($user, $rememberMe = true)
	{
		// log user in
		$loginDuration = $rememberMe ? $this->module->loginDuration : 0;
		Yii::$app->user->login($user, $loginDuration);

		// check for a valid returnUrl (to prevent a weird login bug)
		//   https://github.com/amnah/yii2-user/issues/115
		$loginRedirect = $this->module->loginRedirect;
		$returnUrl = Yii::$app->user->getReturnUrl($loginRedirect);
		if (strpos($returnUrl, "user/login") !== false || strpos($returnUrl, "user/logout") !== false) {
			$returnUrl = null;
		}

		return $returnUrl;
	}

	/**
	 * Log user out and redirect
	 */
	public function actionLogout()
	{
		Yii::$app->user->logout();

		// handle redirect
		$logoutRedirect = $this->module->logoutRedirect;
		if ($logoutRedirect) {
			return $this->redirect($logoutRedirect);
		}
		return $this->goHome();
	}

	/**
	 * Display registration page
	 */
	public function actionRegister()
	{
		/** @var \p2m\users\models\User $user */
		/** @var \p2m\users\models\Profile $profile */
		/** @var \p2m\users\models\Role $role */

		// set up new user/profile objects
		$user = $this->module->model("User", ["scenario" => "register"]);
		$profile = $this->module->model("Profile");

		// load post data
		$post = Yii::$app->request->post();
		if ($user->load($post)) {

			// ensure profile data gets loaded
			$profile->load($post);

			// validate for ajax request
			if (Yii::$app->request->isAjax) {
				Yii::$app->response->format = Response::FORMAT_JSON;
				return ActiveForm::validate($user, $profile);
			}

			// validate for normal request
			if ($user->validate() && $profile->validate()) {

				// perform registration
				$role = $this->module->model("Role");
				$user->setRegisterAttributes($role::ROLE_USER)->save();
				$profile->setUser($user->id)->save();
				$this->afterRegister($user);

				// set flash
				// don't use $this->refresh() because user may automatically be logged in and get 403 forbidden
				$successText = Yii::t("user", "Successfully registered [ {displayName} ]", ["displayName" => $user->getDisplayName()]);
				$guestText = "";
				if (Yii::$app->user->isGuest) {
					$guestText = Yii::t("user", " - Please check your email to confirm your account");
				}
				Yii::$app->session->setFlash("Register-success", $successText . $guestText);
			}
		}

		return $this->render("register", compact("user", "profile"));
	}

	/**
	 * Process data after registration
	 * @param \p2m\users\models\User $user
	 */
	protected function afterRegister($user)
	{
		/** @var \p2m\users\models\UserToken $userToken */
		$userToken = $this->module->model("UserToken");

		// determine userToken type to see if we need to send email
		$userTokenType = null;
		if ($user->status == $user::STATUS_INACTIVE) {
			$userTokenType = $userToken::TYPE_EMAIL_ACTIVATE;
		} elseif ($user->status == $user::STATUS_UNCONFIRMED_EMAIL) {
			$userTokenType = $userToken::TYPE_EMAIL_CHANGE;
		}

		// check if we have a userToken type to process, or just log user in directly
		if ($userTokenType) {
			$userToken = $userToken::generate($user->id, $userTokenType);
			if (!$numSent = $user->sendEmailConfirmation($userToken)) {

				// handle email error
				//Yii::$app->session->setFlash("Email-error", "Failed to send email");
			}
		} else {
			Yii::$app->user->login($user, $this->module->loginDuration);
		}
	}

	/**
	 * Confirm email
	 */
	public function actionConfirm($token)
	{
		/** @var \p2m\users\models\UserToken $userToken */
		/** @var \p2m\users\models\User $user */

		// search for userToken
		$success = false;
		$email = "";
		$userToken = $this->module->model("UserToken");
		$userToken = $userToken::findByToken($token, [$userToken::TYPE_EMAIL_ACTIVATE, $userToken::TYPE_EMAIL_CHANGE]);
		if ($userToken) {

			// find user and ensure that another user doesn't have that email
			//   for example, user registered another account before confirming change of email
			$user = $this->module->model("User");
			$user = $user::findOne($userToken->user_id);
			$newEmail = $userToken->data;
			if ($user->confirm($newEmail)) {
				$success = true;
			}

			// set email and delete token
			$email = $newEmail ?: $user->email;
			$userToken->delete();
		}

		return $this->render("confirm", compact("userToken", "success", "email"));
	}

	/**
	 * Account
	 */
	public function actionAccount()
	{
		/** @var \p2m\users\models\User $user */
		/** @var \p2m\users\models\UserToken $userToken */

		// set up user and load post data
		$user = Yii::$app->user->identity;
		$user->setScenario("account");
		$loadedPost = $user->load(Yii::$app->request->post());

		// validate for ajax request
		if ($loadedPost && Yii::$app->request->isAjax) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($user);
		}

		// validate for normal request
		$userToken = $this->module->model("UserToken");
		if ($loadedPost && $user->validate()) {

			// check if user changed his email
			$newEmail = $user->checkEmailChange();
			if ($newEmail) {
				$userToken = $userToken::generate($user->id, $userToken::TYPE_EMAIL_CHANGE, $newEmail);
				if (!$numSent = $user->sendEmailConfirmation($userToken)) {

					// handle email error
					//Yii::$app->session->setFlash("Email-error", "Failed to send email");
				}
			}

			// save, set flash, and refresh page
			$user->save(false);
			Yii::$app->session->setFlash("Account-success", Yii::t("user", "Account updated"));
			return $this->refresh();
		} else {
			$userToken = $userToken::findByUser($user->id, $userToken::TYPE_EMAIL_CHANGE);
		}

		return $this->render("account", compact("user", "userToken"));
	}

	/**
	 * Profile
	 */
	public function actionProfile()
	{
		/** @var \p2m\users\models\Profile $profile */

		// set up profile and load post data
		$profile = Yii::$app->user->identity->profile;
		$loadedPost = $profile->load(Yii::$app->request->post());

		// validate for ajax request
		if ($loadedPost && Yii::$app->request->isAjax) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($profile);
		}

		// validate for normal request
		if ($loadedPost && $profile->validate()) {
			$profile->save(false);
			Yii::$app->session->setFlash("Profile-success", Yii::t("user", "Profile updated"));
			return $this->refresh();
		}

		return $this->render("profile", compact("profile"));
	}

	/**
	 * Resend email confirmation
	 */
	public function actionResend()
	{
		/** @var \p2m\users\models\ResendForm $model */

		// load post data and send email
		$model = $this->module->model("ResendForm");
		if ($model->load(Yii::$app->request->post()) && $model->sendEmail()) {

			// set flash (which will show on the current page)
			Yii::$app->session->setFlash("Resend-success", Yii::t("user", "Confirmation email resent"));
		}

		return $this->render("resend", compact("model"));
	}

	/**
	 * Resend email change confirmation
	 */
	public function actionResendChange()
	{
		/** @var \p2m\users\models\User $user */
		/** @var \p2m\users\models\UserToken $userToken */

		// find userToken of type email change
		$user = Yii::$app->user->identity;
		$userToken = $this->module->model("UserToken");
		$userToken = $userToken::findByUser($user->id, $userToken::TYPE_EMAIL_CHANGE);
		if ($userToken) {

			// send email and set flash message
			$user->sendEmailConfirmation($userToken);
			Yii::$app->session->setFlash("Resend-success", Yii::t("user", "Confirmation email resent"));
		}

		return $this->redirect(["/user/account"]);
	}

	/**
	 * Cancel email change
	 */
	public function actionCancel()
	{
		/** @var \p2m\users\models\User $user */
		/** @var \p2m\users\models\UserToken $userToken */

		// find userToken of type email change
		$user = Yii::$app->user->identity;
		$userToken = $this->module->model("UserToken");
		$userToken = $userToken::findByUser($user->id, $userToken::TYPE_EMAIL_CHANGE);
		if ($userToken) {
			$userToken->delete();
			Yii::$app->session->setFlash("Cancel-success", Yii::t("user", "Email change cancelled"));
		}

		return $this->redirect(["/user/account"]);
	}

	/**
	 * Forgot password
	 */
	public function actionForgot()
	{
		/** @var \p2m\users\models\ForgotForm $model */

		// load post data and send email
		$model = $this->module->model("ForgotForm");
		if ($model->load(Yii::$app->request->post()) && $model->sendForgotEmail()) {

			// set flash (which will show on the current page)
			Yii::$app->session->setFlash("Forgot-success", Yii::t("user", "Instructions to reset your password have been sent"));
		}

		return $this->render("forgot", compact("model"));
	}

	/**
	 * Reset password
	 */
	public function actionReset($token)
	{
		/** @var \p2m\users\models\User $user */
		/** @var \p2m\users\models\UserToken $userToken */

		// get user token and check expiration
		$userToken = $this->module->model("UserToken");
		$userToken = $userToken::findByToken($token, $userToken::TYPE_PASSWORD_RESET);
		if (!$userToken) {
			return $this->render('reset', ["invalidToken" => true]);
		}

		// get user and set "reset" scenario
		$success = false;
		$user = $this->module->model("User");
		$user = $user::findOne($userToken->user_id);
		$user->setScenario("reset");

		// load post data and reset user password
		if ($user->load(Yii::$app->request->post()) && $user->save()) {

			// delete userToken and set success = true
			$userToken->delete();
			$success = true;
		}

		return $this->render('reset', compact("user", "success"));
	}
}
