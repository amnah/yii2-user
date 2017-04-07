<?php
/**
 * UsersModule.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @license MIT
 *
 * @package p2made/yii2-p2y2-users
 * @class \p2m\users\modules\UsersModule
 */

namespace p2m\users\modules;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

use \p2m\base\helpers\Settings;

class UsersModule extends \yii\base\Module
{
	/**
	 * @var string Module version
	 */
	protected $version = "5.0.6";

	/**
	 * @var string Alias for module
	 */
	public $alias = "@user";

	/**
	 * @var bool If true, users are required to enter an email
	 */
	public $requireEmail = true;

	/**
	 * @var bool If true, users are required to enter a username
	 */
	public $requireUsername = false;

	/**
	 * @var bool If true, users can enter an email. This is automatically set to true if $requireEmail = true
	 */
	public $useEmail = true;

	/**
	 * @var bool If true, users can enter a username. This is automatically set to true if $requireUsername = true
	 */
	public $useUsername = true;

	/**
	 * @var bool If true, users can log in using their email
	 */
	public $loginEmail = true;

	/**
	 * @var bool If true, users can log in using their username
	 */
	public $loginUsername = true;

	/**
	 * @var int Login duration
	 */
	public $loginDuration = 2592000; // 1 month

	/**
	 * @var array|string|null Url to redirect to after logging in. If null, will redirect to home page. Note that
	 *						AccessControl takes precedence over this (see [[yii\web\User::loginRequired()]])
	 */
	public $loginRedirect = null;

	/**
	 * @var array|string|null Url to redirect to after logging out. If null, will redirect to home page
	 */
	public $logoutRedirect = null;

	/**
	 * @var bool If true, users will have to confirm their email address after registering (= email activation)
	 */
	public $emailConfirmation = true;

	/**
	 * @var bool If true, users will have to confirm their email address after changing it on the account page
	 */
	public $emailChangeConfirmation = true;

	/**
	 * @var string Reset password token expiration (passed to strtotime())
	 */
	public $resetExpireTime = "2 days";

	/**
	 * @var string Login via email token expiration (passed to strtotime())
	 */
	public $loginExpireTime = "15 minutes";

	/**
	 * @var string Email view path
	 */
	public $emailViewPath = "@user/mail";

	/**
	 * @var string Force translation
	 */
	public $forceTranslation = false;

	/**
	 * @var array Model classes, e.g., ["User" => "amnah\yii2\user\models\User"]
	 * Usage:
	 *   $user = Yii::$app->getModule("user")->model("User", $config);
	 *   (equivalent to)
	 *   $user = new \amnah\yii2\user\models\User($config);
	 *
	 * The model classes here will be merged with/override the [[getDefaultModelClasses()|default ones]]
	 */
	public $modelClasses = [];

	/**
	 * Get module version
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		// check for valid email/username properties
		$this->checkModuleProperties();

		// set up i8n
		if (empty(Yii::$app->i18n->translations['user'])) {
			Yii::$app->i18n->translations['user'] = [
				'class' => 'yii\i18n\PhpMessageSource',
				'basePath' => __DIR__ . '/messages',
				'forceTranslation' => $this->forceTranslation,
			];
		}

		// override modelClasses
		$this->modelClasses = array_merge($this->getDefaultModelClasses(), $this->modelClasses);

		// set alias
		$this->setAliases([
			$this->alias => __DIR__,
		]);
	}

	/**
	 * Check for valid email/username properties
	 */
	protected function checkModuleProperties()
	{
		// set use fields based on required fields
		if ($this->requireEmail) {
			$this->useEmail = true;
		}
		if ($this->requireUsername) {
			$this->useUsername = true;
		}

		// get class name for error messages
		$className = get_called_class();

		// check required fields
		if (!$this->requireEmail && !$this->requireUsername) {
			throw new InvalidConfigException("{$className}: \$requireEmail and/or \$requireUsername must be true");
		}
		// check login fields
		if (!$this->loginEmail && !$this->loginUsername) {
			throw new InvalidConfigException("{$className}: \$loginEmail and/or \$loginUsername must be true");
		}
		// check email fields with emailConfirmation/emailChangeConfirmation is true
		if (!$this->useEmail && ($this->emailConfirmation || $this->emailChangeConfirmation)) {
			$msg = "{$className}: \$useEmail must be true if \$email(Change)Confirmation is true";
			throw new InvalidConfigException($msg);
		}

		// ensure that the "user" component is set properly
		// this typically causes problems in the yii2-advanced app when people set it in
		// "common/config" instead of "frontend/config" and/or "backend/config"
		//   -> this results in users failing to login without any feedback/error message
		$userComponent = Yii::$app->get('user', false);
		if ($userComponent && !$userComponent instanceof \amnah\yii2\user\components\User) {
			throw new InvalidConfigException('Yii::$app->user is not set properly. It needs to extend \amnah\yii2\user\components\User');
		}
	}

	/**
	 * Get default model classes
	 */
	protected function getDefaultModelClasses()
	{
		// attempt to calculate user class based on user component
		//   (we do this because yii console does not have a user component)
		if (Yii::$app->get('user', false)) {
			$userClass = Yii::$app->user->identityClass;
		} elseif (class_exists('app\models\User')) {
			$userClass = 'app\models\User';
		} else {
			$userClass = 'amnah\yii2\user\models\User';
		}

		return [
			'User' => $userClass,
			'Profile' => 'amnah\yii2\user\models\Profile',
			'Role' => 'amnah\yii2\user\models\Role',
			'UserToken' => 'amnah\yii2\user\models\UserToken',
			'UserAuth' => 'amnah\yii2\user\models\UserAuth',
			'ForgotForm' => 'amnah\yii2\user\models\forms\ForgotForm',
			'LoginForm' => 'amnah\yii2\user\models\forms\LoginForm',
			'ResendForm' => 'amnah\yii2\user\models\forms\ResendForm',
			'UserSearch' => 'amnah\yii2\user\models\search\UserSearch',
			'LoginEmailForm' => 'amnah\yii2\user\models\forms\LoginEmailForm',
		];
	}

	/**
	 * Get object instance of model
	 * @param string $name
	 * @param array  $config
	 * @return ActiveRecord
	 */
	public function model($name, $config = [])
	{
		$config["class"] = $this->modelClasses[ucfirst($name)];
		return Yii::createObject($config);
	}

	/**
	 * Modify createController() to handle routes in the default controller
	 *
	 * This is needed because of the way we map actions to "user/default/<action>".
	 * We can't use module bootstrapping because that doesn't work when
	 * `urlManager.enablePrettyUrl` = false.
	 * Additionally, this requires one less step during installation
	 *
	 * @link https://github.com/amnah/yii2-user/issues/94
	 *
	 * "user", "user/default", "user/admin", "user/copy", and "user/auth" work like normal
	 * any other "user/xxx" gets changed to "user/default/xxx"
	 *
	 * @inheritdoc
	 */
	public function createController($route)
	{
		// check valid routes
		$validRoutes  = [$this->defaultRoute, "admin", "copy", "auth"];
		$isValidRoute = false;
		foreach ($validRoutes as $validRoute) {
			if (strpos($route, $validRoute) === 0) {
				$isValidRoute = true;
				break;
			}
		}

		return (empty($route) or $isValidRoute)
			? parent::createController($route)
			: parent::createController("{$this->defaultRoute}/{$route}");
	}

	/**
	 * Get a list of actions for this module. Used for debugging/initial installations
	 */
	public function getActions()
	{
		return [
			"/{$this->id}" => "This 'actions' list. Appears only when <strong>YII_DEBUG</strong>=true, otherwise redirects to /login or /account",
			"/{$this->id}/admin" => "Admin CRUD",
			"/{$this->id}/login" => "Login page",
			"/{$this->id}/logout" => "Logout page",
			"/{$this->id}/register" => "Register page",
			"/{$this->id}/login-email" => "Login page v2 (login/register via email link)",
			"/{$this->id}/login-callback?token=zzzzz" => "Login page v2 callback (after user clicks link in email)",
			"/{$this->id}/auth/login?authclient=facebook" => "Register/login via social account",
			"/{$this->id}/auth/connect?authclient=facebook" => "Connect social account to currently logged in user",
			"/{$this->id}/account" => "User account page (email, username, password)",
			"/{$this->id}/profile" => "Profile page",
			"/{$this->id}/forgot" => "Forgot password page",
			"/{$this->id}/reset?token=zzzzz" => "Reset password page. Automatically generated from forgot password page",
			"/{$this->id}/resend" => "Resend email confirmation (for both activation and change of email)",
			"/{$this->id}/resend-change" => "Resend email change confirmation (quick link on the 'Account' page)",
			"/{$this->id}/cancel" => "Cancel email change confirmation (quick link on the 'Account' page)",
			"/{$this->id}/confirm?token=zzzzz" => "Confirm email address. Automatically generated upon registration/email change",
		];
	}
}
?>


<?php
class UsersModule extends \yii\base\Module
{
	/**
	 * @var string package version
	 */
	private static $_version = '0.1.5';

	/**
	 * @var string Alias for module
	 */
	public static $alias = '@user';

	private static $_requireEmail;
	private static $_requireUsername;
	private static $_useEmail;
	private static $_useUsername;
	private static $_loginEmail;
	private static $_loginUsername;
	private static $_loginDuration;
	private static $_loginRedirect;
	private static $_logoutRedirect;
	private static $_emailConfirmation;
	private static $_emailChangeConfirmation;
	private static $_resetExpireTime;
	private static $_loginExpireTime;
	private static $_emailViewPath;
	private static $_forceTranslation;
	private static $_modelClasses;

	/**
	 * Get package version
	 * @return string
	 */
	public function getVersion()
	{
		return $_version;
	}

	/**
	 * @var bool If true, users are required to enter an email
	 */
	public static function requireEmail() // default true
	{
		if(isset($_requireEmail)) {
			return $_requireEmail;
		}

		// using 'p2m' as param space
		$_requireEmail = (
			isset(\Yii::$app->params['p2m']['users']['requireEmail']) ?
			\Yii::$app->params['p2m']['users']['requireEmail'] : true
		);

		return $_requireEmail;
	}

	/**
	 * @var bool If true, users are required to enter a username
	 */
	public static function requireUsername() // default false
	{
		if(isset($_requireUsername)) {
			return $_requireUsername;
		}

		// using 'p2m' as param space
		$_requireUsername = (
			isset(\Yii::$app->params['p2m']['users']['requireUsername']) ?
			\Yii::$app->params['p2m']['users']['requireUsername'] : false
		);

		return $_requireUsername;
	}

	/**
	 * @var bool If true, users can enter an email.
	 * This is automatically set to true if $requireEmail = true
	 */
	public static function useEmail() // default true
	{
		if(isset($_useEmail)) {
			return $_useEmail;
		}

		// using 'p2m' as param space
		$_useEmail = (
			isset(\Yii::$app->params['p2m']['users']['useEmail']) ?
			\Yii::$app->params['p2m']['users']['useEmail'] : true
		);

		return $_useEmail;
	}

	/**
	 * @var bool If true, users can enter a username.
	 * This is automatically set to true if $requireUsername = true
	 */
	public static function useUsername() // default true
	{
		if(isset($_useUsername)) {
			return $_useUsername;
		}

		// using 'p2m' as param space
		$_useUsername = (
			isset(\Yii::$app->params['p2m']['users']['useUsername']) ?
			\Yii::$app->params['p2m']['users']['useUsername'] : true
		);

		return $_useUsername;
	}

	/**
	 * @var bool If true, users can log in using their email
	 */
	public static function loginEmail() // default true
	{
		if(isset($_loginEmail)) {
			return $_loginEmail;
		}

		// using 'p2m' as param space
		$_loginEmail = (
			isset(\Yii::$app->params['p2m']['users']['loginEmail']) ?
			\Yii::$app->params['p2m']['users']['loginEmail'] : true
		);

		return $_loginEmail;
	}

	/**
	 * @var bool If true, users can log in using their username
	 */
	public static function loginUsername() // default true
	{
		if(isset($_loginUsername)) {
			return $_loginUsername;
		}

		// using 'p2m' as param space
		$_loginUsername = (
			isset(\Yii::$app->params['p2m']['users']['loginUsername']) ?
			\Yii::$app->params['p2m']['users']['loginUsername'] : true
		);

		return $_loginUsername;
	}

	/**
	 * @var int Login duration
	 */
	public static function loginDuration() // default 2551443 - one mean lunar month
	{
		if(isset($_loginDuration)) {
			return $_loginDuration;
		}

		// using 'p2m' as param space
		$_loginDuration = (
			isset(\Yii::$app->params['p2m']['users']['loginDuration']) ?
			\Yii::$app->params['p2m']['users']['loginDuration'] : 2551443 // one mean lunar month
		);

		return $_loginDuration;
	}

	/**
	 * @var array|string|null Url to redirect to after logging in.
	 * If null, will redirect to home page. Note that AccessControl takes precedence
	 * over this (see [[yii\web\User::loginRequired()]]).
	 */
	public static function loginRedirect() // default null
	{
		if(isset($_loginRedirect)) {
			return $_loginRedirect;
		}

		// using 'p2m' as param space
		$_loginRedirect = (
			isset(\Yii::$app->params['p2m']['users']['loginRedirect']) ?
			\Yii::$app->params['p2m']['users']['loginRedirect'] : null
		);

		return $_loginRedirect;
	}

	/**
	 * @var array|string|null Url to redirect to after logging out.
	 * If null, will redirect to home page
	 */
	public static function logoutRedirect() // default null
	{
		if(isset($_logoutRedirect)) {
			return $_logoutRedirect;
		}

		// using 'p2m' as param space
		$_logoutRedirect = (
			isset(\Yii::$app->params['p2m']['users']['logoutRedirect']) ?
			\Yii::$app->params['p2m']['users']['logoutRedirect'] : null
		);

		return $_logoutRedirect;
	}

	/**
	 * @var bool If true, users will have to confirm their email address after registering (= email activation)
	 */
	public static function emailConfirmation() // default true
	{
		if(isset($_emailConfirmation)) {
			return $_emailConfirmation;
		}

		// using 'p2m' as param space
		$_emailConfirmation = (
			isset(\Yii::$app->params['p2m']['users']['emailConfirmation']) ?
			\Yii::$app->params['p2m']['users']['emailConfirmation'] : true
		);

		return $_emailConfirmation;
	}

	/**
	 * @var bool If true, users will have to confirm their
	 * email address after changing it on the account page
	 */
	public static function emailChangeConfirmation() // default true
	{
		if(isset($_emailChangeConfirmation)) {
			return $_emailChangeConfirmation;
		}

		// using 'p2m' as param space
		$_emailChangeConfirmation = (
			isset(\Yii::$app->params['p2m']['users']['emailChangeConfirmation']) ?
			\Yii::$app->params['p2m']['users']['emailChangeConfirmation'] : true
		);

		return $_emailChangeConfirmation;
	}

	/**
	 * @var string Reset password token expiration (passed to strtotime())
	 */
	public static function resetExpireTime() // default '2 days'
	{
		if(isset($_resetExpireTime)) {
			return $_resetExpireTime;
		}

		// using 'p2m' as param space
		$_resetExpireTime = (
			isset(\Yii::$app->params['p2m']['users']['resetExpireTime']) ?
			\Yii::$app->params['p2m']['users']['resetExpireTime'] : '2 days'
		);

		return $_resetExpireTime;
	}

	/**
	 * @var string Login via email token expiration (passed to strtotime())
	 */
	public static function loginExpireTime() // default '15 minutes'
	{
		if(isset($_loginExpireTime)) {
			return $_loginExpireTime;
		}

		// using 'p2m' as param space
		$_loginExpireTime = (
			isset(\Yii::$app->params['p2m']['users']['loginExpireTime']) ?
			\Yii::$app->params['p2m']['users']['loginExpireTime'] : '15 minutes'
		);

		return $_loginExpireTime;
	}

	/**
	 * @var string Email view path
	 */
	public static function emailViewPath() // default true
	{
		if(isset($_emailViewPath)) {
			return $_emailViewPath;
		}

		// using 'p2m' as param space
		$_emailViewPath = (
			isset(\Yii::$app->params['p2m']['users']['emailViewPath']) ?
			\Yii::$app->params['p2m']['users']['emailViewPath'] : true
		);

		return $_emailViewPath;
	}

	/**
	 * @var string Force translation
	 */
	public static function forceTranslation() // default false
	{
		if(isset($_forceTranslation)) {
			return $_forceTranslation;
		}

		// using 'p2m' as param space
		$_forceTranslation = (
			isset(\Yii::$app->params['p2m']['users']['forceTranslation']) ?
			\Yii::$app->params['p2m']['users']['forceTranslation'] : false
		);

		return $_forceTranslation;
	}

	/**
	 * @var array Model classes, e.g., ["User" => "p2m\users\models\User"]
	 * Usage:
	 *   $user = Yii::$app->getModule("user")->model("User", $config);
	 *   (equivalent to)
	 *   $user = new \p2m\users\models\User($config);
	 *
	 * The model classes here will be merged with/override the [[getDefaultModelClasses()|default ones]]
	 */
	public static function modelClasses() // default []
	{
		if(isset($_modelClasses)) {
			return $_modelClasses;
		}

		// using 'p2m' as param space
		$_modelClasses = (
			isset(\Yii::$app->params['p2m']['users']['modelClasses']) ?
			\Yii::$app->params['p2m']['users']['modelClasses'] : []
		);

		return $_modelClasses;
	}

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		// check for valid email/username properties
		$this->checkModuleProperties();

		// set up i8n
		if (empty(Yii::$app->i18n->translations['user'])) {
			Yii::$app->i18n->translations['user'] = [
				'class' => 'yii\i18n\PhpMessageSource',
				'basePath' => __DIR__ . '/messages',
				'forceTranslation' => $this->forceTranslation,
			];
		}

		// override modelClasses
		$this->modelClasses = array_merge($this->getDefaultModelClasses(), $this->modelClasses);

		// set alias
		$this->setAliases([
			$this->alias => __DIR__,
		]);
	}

	/**
	 * Check for valid email/username properties
	 */
	protected function checkModuleProperties()
	{
		// set use fields based on required fields
		if ($this->requireEmail) {
			$this->useEmail = true;
		}
		if ($this->requireUsername) {
			$this->useUsername = true;
		}

		// get class name for error messages
		$className = get_called_class();

		// check required fields
		if (!$this->requireEmail && !$this->requireUsername) {
			throw new InvalidConfigException("{$className}: \$requireEmail and/or \$requireUsername must be true");
		}
		// check login fields
		if (!$this->loginEmail && !$this->loginUsername) {
			throw new InvalidConfigException("{$className}: \$loginEmail and/or \$loginUsername must be true");
		}
		// check email fields with emailConfirmation/emailChangeConfirmation is true
		if (!$this->useEmail && ($this->emailConfirmation || $this->emailChangeConfirmation)) {
			$msg = "{$className}: \$useEmail must be true if \$email(Change)Confirmation is true";
			throw new InvalidConfigException($msg);
		}

		// ensure that the "user" component is set properly
		// this typically causes problems in the yii2-advanced app when people set it in
		// "common/config" instead of "frontend/config" and/or "backend/config"
		//   -> this results in users failing to login without any feedback/error message
		$userComponent = Yii::$app->get('user', false);
		if ($userComponent && !$userComponent instanceof \p2m\users\components\User) {
			throw new InvalidConfigException('Yii::$app->user is not set properly. It needs to extend \p2m\users\components\User');
		}
	}

	/**
	 * Get default model classes
	 */
	protected function getDefaultModelClasses()
	{
		// attempt to calculate user class based on user component
		//   (we do this because yii console does not have a user component)
		if (Yii::$app->get('user', false)) {
			$userClass = Yii::$app->user->identityClass;
		} elseif (class_exists('app\models\User')) {
			$userClass = 'app\models\User';
		} else {
			$userClass = 'p2m\users\models\User';
		}

		return [
			'User' => $userClass,
			'Profile' => 'p2m\users\models\Profile',
			'Role' => 'p2m\users\models\Role',
			'UserToken' => 'p2m\users\models\UserToken',
			'UserAuth' => 'p2m\users\models\UserAuth',
			'ForgotForm' => 'p2m\users\models\ForgotForm',
			'LoginForm' => 'p2m\users\models\LoginForm',
			'ResendForm' => 'p2m\users\models\ResendForm',
			'UserSearch' => 'p2m\users\models\search\UserSearch',
			'LoginEmailForm' => 'p2m\users\models\LoginEmailForm',
		];
	}

	/**
	 * Get object instance of model
	 * @param string $name
	 * @param array  $config
	 * @return ActiveRecord
	 */
	public function model($name, $config = [])
	{
		$config["class"] = $this->modelClasses[ucfirst($name)];
		return Yii::createObject($config);
	}

	/**
	 * Modify createController() to handle routes in the default controller
	 *
	 * This is needed because of the way we map actions to "user/default/<action>".
	 * We can't use module bootstrapping because that doesn't work when
	 * `urlManager.enablePrettyUrl` = false.
	 * Additionally, this requires one less step during installation
	 *
	 * @link https://github.com/amnah/yii2-user/issues/94
	 *
	 * "user", "user/default", "user/admin", "user/copy", and "user/auth" work like normal
	 * any other "user/xxx" gets changed to "user/default/xxx"
	 *
	 * @inheritdoc
	 */
	public function createController($route)
	{
		// check valid routes
		$validRoutes  = [$this->defaultRoute, "admin", "copy", "auth"];
		$isValidRoute = false;
		foreach ($validRoutes as $validRoute) {
			if (strpos($route, $validRoute) === 0) {
				$isValidRoute = true;
				break;
			}
		}

		return (empty($route) or $isValidRoute)
			? parent::createController($route)
			: parent::createController("{$this->defaultRoute}/{$route}");
	}

	/**
	 * Get a list of actions for this module. Used for debugging/initial installations
	 */
	public function getActions()
	{
		return [
			"/{$this->id}" => "This 'actions' list. Appears only when <strong>YII_DEBUG</strong>=true, otherwise redirects to /login or /account",
			"/{$this->id}/admin" => "Admin CRUD",
			"/{$this->id}/login" => "Login page",
			"/{$this->id}/logout" => "Logout page",
			"/{$this->id}/register" => "Register page",
			"/{$this->id}/login-email" => "Login page v2 (login/register via email link)",
			"/{$this->id}/login-callback?token=zzzzz" => "Login page v2 callback (after user clicks link in email)",
			"/{$this->id}/auth/login?authclient=facebook" => "Register/login via social account",
			"/{$this->id}/auth/connect?authclient=facebook" => "Connect social account to currently logged in user",
			"/{$this->id}/account" => "User account page (email, username, password)",
			"/{$this->id}/profile" => "Profile page",
			"/{$this->id}/forgot" => "Forgot password page",
			"/{$this->id}/reset?token=zzzzz" => "Reset password page. Automatically generated from forgot password page",
			"/{$this->id}/resend" => "Resend email confirmation (for both activation and change of email)",
			"/{$this->id}/resend-change" => "Resend email change confirmation (quick link on the 'Account' page)",
			"/{$this->id}/cancel" => "Cancel email change confirmation (quick link on the 'Account' page)",
			"/{$this->id}/confirm?token=zzzzz" => "Confirm email address. Automatically generated upon registration/email change",
		];
	}
}
?>


