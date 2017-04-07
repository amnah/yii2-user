<?php

namespace amnah\yii2\user\controllers;

use Yii;
use yii\web\Controller;

/**
 * Social auth controller for User module
 */
class AuthController extends Controller
{
	/**
	 * @var \amnah\yii2\user\Module
	 * @inheritdoc
	 */
	public $module;

	/**
	 * @inheritdoc
	 */
	public function actions()
	{
		return [
			'connect' => [
				'class' => 'yii\authclient\AuthAction',
				'successCallback' => [$this, 'connectCallback'],
				'successUrl' => Yii::$app->homeUrl . 'user/account',
			],
			'login' => [
				'class' => 'yii\authclient\AuthAction',
				'successCallback' => [$this, 'loginRegisterCallback'],
				'successUrl' => Yii::$app->homeUrl,
			],
		];
	}

	/**
	 * Connect social auth to the logged-in user
	 * @param \yii\authclient\BaseClient $client
	 * @return \yii\web\Response
	 * @throws \yii\web\ForbiddenHttpException
	 */
	public function connectCallback($client)
	{
		// uncomment this to see which attributes you get back
		//echo "<pre>";print_r($client->getUserAttributes());echo "</pre>";exit;

		// check if user is not logged in. if so, do nothing
		if (Yii::$app->user->isGuest) {
			return;
		}

		// register a new user
		$userAuth = $this->initUserAuth($client);
		$userAuth->setUser(Yii::$app->user->id)->save();
	}

	/**
	 * Login/register via social auth
	 * @param \yii\authclient\BaseClient $client
	 * @return \yii\web\Response
	 * @throws \yii\web\ForbiddenHttpException
	 */
	public function loginRegisterCallback($client)
	{
		// uncomment this to see which attributes you get back
		//echo "<pre>";print_r($client->getUserAttributes());echo "</pre>";exit;

		// check if user is already logged in. if so, do nothing
		if (!Yii::$app->user->isGuest) {
			return;
		}

		// attempt to log in as an existing user
		if ($this->attemptLogin($client)) {
			return;
		}

		// register a new user
		$userAuth = $this->initUserAuth($client);
		$this->registerAndLoginUser($client, $userAuth);
	}

	/**
	 * Initialize a userAuth model based on $client data. Note that we don't set
	 * `user_id` yet because that can either be the currently logged in user OR a user
	 * matched by email address
	 * @param \yii\authclient\BaseClient $client
	 * @return \amnah\yii2\user\models\UserAuth
	 */
	protected function initUserAuth($client)
	{
		/** @var \amnah\yii2\user\models\UserAuth $userAuth */

		// build data. note that we don't set `user_id` yet
		$attributes = $client->getUserAttributes();
		$userAuth = $this->module->model("UserAuth");
		$userAuth->provider = $client->name;
		$userAuth->provider_id = (string)$attributes["id"];

		// parse out google id
		$idCheck = strpos($userAuth->provider_id, "o8/id?id=");
		if ($idCheck !== false) {
			$userAuth->provider_id = substr($userAuth->provider_id, $idCheck + 9);
		}

		$userAuth->setProviderAttributes($attributes);
		return $userAuth;
	}

	/**
	 * Attempt to log user in by checking if $userAuth already exists in the db,
	 * or if a user already has the email address
	 * @param \yii\authclient\BaseClient $client
	 * @return bool
	 */
	protected function attemptLogin($client)
	{
		/** @var \amnah\yii2\user\models\User $user */
		/** @var \amnah\yii2\user\models\UserAuth $userAuth */
		/** @var \amnah\yii2\user\models\UserToken $userToken */
		$user = $this->module->model("User");
		$userAuth = $this->module->model("UserAuth");
		$userToken = $this->module->model("UserToken");

		// attempt to find userAuth in database by id and name
		$attributes = $client->getUserAttributes();
		$userAuth = $userAuth::findOne([
			"provider" => $client->name,
			"provider_id" => (string)$attributes["id"],
		]);
		if ($userAuth) {

			// check if user is banned, otherwise log in
			$user = $user::findOne($userAuth->user_id);
			if ($user && $user->banned_at) {
				return false;
			}

			Yii::$app->user->login($user, $this->module->loginDuration);
			return true;
		}

		// call "setInfo{clientName}" function to ensure that we get email consistently
		// this is mainly used for google auth, which returns the email in an array
		// @see setInfoGoogle()
		$function = "setInfo" . ucfirst($client->name);
		list ($user, $profile) = $this->$function($attributes);

		// attempt to find user by email
		if (!empty($user["email"])) {

			// check if any user has has tried to change their email
			// if so, delete it
			$email = trim($user["email"]);
			$userToken = $userToken::findByData($email, $userToken::TYPE_EMAIL_CHANGE);
			if ($userToken) {
				$userToken->delete();
			}

			// find user and create user provider for match
			$user = $user::findOne(["email" => $email]);
			if ($user) {
				$userAuth = $this->initUserAuth($client);
				$userAuth->setUser($user->id)->save();
				Yii::$app->user->login($user, $this->module->loginDuration);
				return true;
			}
		}

		return false;
	}

	/**
	 * Register a new user using client attributes and then associate userAuth
	 * @param \yii\authclient\BaseClient $client
	 * @param \amnah\yii2\user\models\UserAuth $userAuth
	 */
	protected function registerAndLoginUser($client, $userAuth)
	{
		/** @var \amnah\yii2\user\models\User $user */
		/** @var \amnah\yii2\user\models\Profile $profile */
		/** @var \amnah\yii2\user\models\Role $role */
		$role = $this->module->model("Role");

		// set user and profile info
		$attributes = $client->getUserAttributes();
		$function = "setInfo" . ucfirst($client->name); // "setInfoFacebook()"
		list ($user, $profile) = $this->$function($attributes);

		// calculate and double check username (in case it is already taken)
		$fallbackUsername = "{$client->name}_{$userAuth->provider_id}";
		$user = $this->doubleCheckUsername($user, $fallbackUsername);

		// save new models
		$user->setScenario("social");
		$user->setRegisterAttributes($role::ROLE_USER, $user::STATUS_ACTIVE)->save();
		$profile->setUser($user->id)->save();
		$userAuth->setUser($user->id)->save();

		// log user in
		Yii::$app->user->login($user, $this->module->loginDuration);
	}

	/**
	 * Double checks username to ensure that it isn't already taken. If so,
	 * revert to fallback
	 * @param \amnah\yii2\user\models\User $user
	 * @param string $fallbackUsername
	 * @return mixed
	 */
	protected function doubleCheckUsername($user, $fallbackUsername)
	{
		// replace periods with underscore to match user rules
		$user->username = str_replace(".", "_", $user->username);

		// check unique username
		$userCheck = $user::findOne(["username" => $user->username]);
		if ($userCheck) {
			$user->username = $fallbackUsername;
		}
		return $user;
	}

	/**
	 * Set info for facebook registration
	 * @param array $attributes
	 * @return array [$user, $profile]
	 */
	protected function setInfoFacebook($attributes)
	{
		/** @var \amnah\yii2\user\models\User $user */
		/** @var \amnah\yii2\user\models\Profile $profile */
		$user = $this->module->model("User");
		$profile = $this->module->model("Profile");

		// set email/username if they are set
		// note: email may be missing if user signed up using a phone number
		if (!empty($attributes["email"])) {
			$user->email = $attributes["email"];
		}
		if (!empty($attributes["username"])) {
			$user->username = $attributes["username"];
		}

		// use facebook name as username as fallback
		if (empty($attributes["email"]) && empty($attributes["username"])) {
			$user->username = str_replace(" ", "_", $attributes["name"]);
		}

		$profile->full_name = $attributes["name"];

		return [$user, $profile];
	}

	/**
	 * Set info for twitter registration
	 * @param array $attributes
	 * @return array [$user, $profile]
	 */
	protected function setInfoTwitter($attributes)
	{
		/** @var \amnah\yii2\user\models\User $user */
		/** @var \amnah\yii2\user\models\Profile $profile */
		$user = $this->module->model("User");
		$profile = $this->module->model("Profile");

		$user->username = $attributes["screen_name"];
		$profile->full_name = $attributes["name"];

		return [$user, $profile];
	}

	/**
	 * Set info for google registration
	 * @param array $attributes
	 * @return array [$user, $profile]
	 */
	protected function setInfoGoogle($attributes)
	{
		/** @var \amnah\yii2\user\models\User $user */
		/** @var \amnah\yii2\user\models\Profile $profile */
		$user = $this->module->model("User");
		$profile = $this->module->model("Profile");

		$user->email = $attributes["emails"][0]["value"];
		$profile->full_name = "{$attributes["name"]["givenName"]} {$attributes["name"]["familyName"]}";

		return [$user, $profile];
	}

	/**
	 * Set info for reddit registration
	 * @param array $attributes
	 * @return array [$user, $profile]
	 */
	protected function setInfoReddit($attributes)
	{
		/** @var \amnah\yii2\user\models\User $user */
		/** @var \amnah\yii2\user\models\Profile $profile */
		$user = $this->module->model("User");
		$profile = $this->module->model("Profile");

		$user->username = $attributes["name"];

		return [$user, $profile];
	}

	/**
	 * Set info for LinkedIn registration
	 * @param array $attributes
	 * @return array [$user, $profile]
	 */
	protected function setInfoLinkedIn($attributes)
	{
		/** @var \amnah\yii2\user\models\User $user */
		/** @var \amnah\yii2\user\models\Profile $profile */
		$user = $this->module->model("User");
		$profile = $this->module->model("Profile");

		$user->email = $attributes["email"];
		$profile->full_name = "{$attributes["first_name"]} {$attributes["last_name"]}";

		return [$user, $profile];
	}

	/**
	 * Set info for vkontakte registration
	 *
	 * @author Ilya Sheershoff <sheershoff@gmail.com>
	 * @param array $attributes
	 * @return array [$user, $profile]
	 */
	protected function setInfoVkontakte($attributes)
	{
		/** @var \amnah\yii2\user\models\User $user */
		/** @var \amnah\yii2\user\models\Profile $profile */
		$user = $this->module->model("User");
		$profile = $this->module->model("Profile");

		foreach ($_SESSION as $k => $v) {
			if (is_object($v) && get_class($v) == "yii\\authclient\\OAuthToken") {
				/** @var \yii\authclient\OAuthToken $v */
				$user->email = $v->getParam('email');
			}
		}

		// set email/username if they are set
		// note: email may be missing if user signed up using a phone number
		if (!empty($attributes["email"])) {
			$user->email = $attributes["email"];
		}
		if (!empty($attributes["username"])) {
			$user->username = $attributes["username"];
		}else{
			$user->username = "vkontakte_{$attributes["id"]}";
		}

		$profile->full_name = "{$attributes["first_name"]} {$attributes["last_name"]}";

		return [$user, $profile];
	}
}
