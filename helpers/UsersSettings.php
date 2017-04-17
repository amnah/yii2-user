<?php
/**
 * UsersSettings.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @license MIT
 *
 * @package p2made/yii2-p2y2-users
 * @class \p2m\users\helpers\UsersSettings
 */

namespace p2m\users\helpers;

use Yii;
use DateTime;
use DateTimeZone;
use yii\helpers\ArrayHelper;

/**
 * Settings for p2m users
 */
class UsersSettings extends \p2m\base\helpers\P2Settings
{
	/**
	 * @var string package version
	 */
	private static $_version = '0.1.5';

	/**
	 * @var string Alias for module
	 */
	public static $alias = '@user';

	/**
	 * Constants define identifiers for settings block
	 */
	const BLOCK_NAME = 'users';

	const REQUIRE_EMAIL =                       'requireEmail';
	const REQUIRE_USERNAME =                    'requireUsername';
	const USE_EMAIL =                           'useEmail';
	const USE_USERNAME =                        'useUsername';
	const LOGIN_EMAIL =                         'loginEmail';
	const LOGIN_USERNAME =                      'loginUsername';
	const LOGIN_DURATION =                      'loginDuration';
	const LOGIN_REDIRECT =                      'loginRedirect';
	const LOGOUT_REDIRECT =                     'logoutRedirect';
	const EMAIL_CONFIRMATION =                  'emailConfirmation';
	const EMAIL_CHANGE_CONFIRMATION =           'emailChangeConfirmation';
	const RESET_EXPIRE_TIME =                   'resetExpireTime';
	const LOGIN_EXPIRE_TIME =                   'loginExpireTime';
	const EMAIL_VIEW_PATH =                     'emailViewPath';
	const FORCE_TRANSLATION =                   'forceTranslation';
	const MODEL_CLASSES =                       'modelClasses';

	/**
	 * Constants define defaults for settings block
	 */
	const DEFAULT_REQUIRE_EMAIL =               true;
	const DEFAULT_REQUIRE_USERNAME =            false;
	const DEFAULT_USE_EMAIL =                   true;
	const DEFAULT_USE_USERNAME =                true;
	const DEFAULT_LOGIN_EMAIL =                 true;
	const DEFAULT_LOGIN_USERNAME =              true;
	const DEFAULT_LOGIN_DURATION =              2551443; // one mean lunar month
	const DEFAULT_LOGIN_REDIRECT =              null;
	const DEFAULT_LOGOUT_REDIRECT =             null;
	const DEFAULT_EMAIL_CONFIRMATION =          true;
	const DEFAULT_EMAIL_CHANGE_CONFIRMATION =   true;
	const DEFAULT_RESET_EXPIRE_TIME =           '2 days';
	const DEFAULT_LOGIN_EXPIRE_TIME =           '15 minutes';
	const DEFAULT_EMAIL_VIEW_PATH =             '@user/mail';
	const DEFAULT_FORCE_TRANSLATION =           false;
	const DEFAULT_MODEL_CLASSES =               [];

	/**
	 * @var array | false users settings
	 */
	private static $_usersSettings;

	/**
	 * @var bool If true, users are required to enter an email
	 */
	private static $_requireEmail;

	/**
	 * @var bool If true, users are required to enter a username
	 */
	private static $_requireUsername;

	/**
	 * @var bool If true, users can enter an email. This is automatically set to true if $requireEmail = true
	 */
	private static $_useEmail;

	/**
	 * @var bool If true, users can enter a username. This is automatically set to true if $requireUsername = true
	 */
	private static $_useUsername;

	/**
	 * @var bool If true, users can log in using their email
	 */
	private static $_loginEmail;

	/**
	 * @var bool If true, users can log in using their username
	 */
	private static $_loginUsername;

	/**
	 * @var int Login duration
	 */
	private static $_loginDuration;

	/**
	 * @var array|string|null Url to redirect to after logging in.
	 * If null, will redirect to home page.
	 * Note that AccessControl takes precedence over this
	 * (see [[yii\web\User::loginRequired()]])
	 */
	private static $_loginRedirect;

	/**
	 * @var array|string|null Url to redirect to after logging out.
	 * If null, will redirect to home page
	 */
	private static $_logoutRedirect;

	/**
	 * @var bool If true, users will have to confirm their
	 * email address after registering (= email activation)
	 */
	private static $_emailConfirmation;

	/**
	 * @var bool If true, users will have to confirm their
	 * email address after changing it on the account page
	 */
	private static $_emailChangeConfirmation;

	/**
	 * @var string Reset password token expiration (passed to strtotime())
	 */
	private static $_resetExpireTime;

	/**
	 * @var string Login via email token expiration (passed to strtotime())
	 */
	private static $_loginExpireTime;

	/**
	 * @var string Email view path
	 */
	private static $_emailViewPath;

	/**
	 * @var string Force translation
	 */
	private static $_forceTranslation;

	/**
	 * @var array Model classes, e.g., ["User" => "amnah\yii2\user\models\User"]
	 * Usage:
	 *   $user = Yii::$app->getModule("user")->model("User", $config);
	 *   (equivalent to)
	 *   $user = new \amnah\yii2\user\models\User($config);
	 *
	 * The model classes here will be merged with/override
	 * the [[getDefaultModelClasses()|default ones]]
	 */
	private static $_modelClasses;

	/**
	 * Get package version
	 * @return string
	 */
	public static function getUsersVersion()
	{
		return self::$_version;
	}

	/**
	 * Get users settings
	 * @return array | false
	 * @default false
	 */
	public static function usersSettings()
	{
		if(isset(self::$_usersSettings)) {
			return self::$_usersSettings;
		}

		return self::getSettingsBlock(self::BLOCK_NAME);
	}

	/**
	 * Get requireEmail setting -
	 * @return boolean
	 * @default true
	 */
	public static function requireEmail()
	{
		if(isset(self::$_requireEmail)) {
			return self::$_requireEmail;
		}

		return self::getSettingsItem(
			self::$_requireEmail,
			self::usersSettings(),
			self::REQUIRE_EMAIL,
			self::DEFAULT_REQUIRE_EMAIL
		);
	}

	/**
	 * Get requireUsername setting -
	 * @return boolean
	 * @default false
	 */
	public static function requireUsername()
	{
		if(isset(self::$_requireUsername)) {
			return self::$_requireUsername;
		}

		return self::getSettingsItem(
			self::$_requireUsername,
			self::usersSettings(),
			self::REQUIRE_USERNAME,
			self::DEFAULT_REQUIRE_USERNAME
		);
	}

	/**
	 * Get useEmail setting -
	 * @return boolean
	 * @default true
	 */
	public static function useEmail()
	{
		if(isset(self::$_useEmail)) {
			return self::$_useEmail;
		}

		return self::getSettingsItem(
			self::$_useEmail,
			self::usersSettings(),
			self::USE_EMAIL,
			self::DEFAULT_USE_EMAIL
		);
	}

	/**
	 * Get useUsername setting -
	 * @return boolean
	 * @default true
	 */
	public static function useUsername()
	{
		if(isset(self::$_useUsername)) {
			return self::$_useUsername;
		}

		return self::getSettingsItem(
			self::$_useUsername,
			self::usersSettings(),
			self::USE_USERNAME,
			self::DEFAULT_USE_USERNAME
		);
	}

	/**
	 * Get loginEmail setting -
	 * @return boolean
	 * @default true
	 */
	public static function loginEmail()
	{
		if(isset(self::$_loginEmail)) {
			return self::$_loginEmail;
		}

		return self::getSettingsItem(
			self::$_loginEmail,
			self::usersSettings(),
			self::LOGIN_EMAIL,
			self::DEFAULT_LOGIN_EMAIL
		);
	}

	/**
	 * Get loginUsername setting -
	 * @return boolean
	 * @default true
	 */
	public static function loginUsername()
	{
		if(isset(self::$_loginUsername)) {
			return self::$_loginUsername;
		}

		return self::getSettingsItem(
			self::$_loginUsername,
			self::usersSettings(),
			self::LOGIN_USERNAME,
			self::DEFAULT_LOGIN_USERNAME
		);
	}

	/**
	 * Get loginDuration setting -
	 * @return boolean
	 * @default 2551443 - one mean lunar month
	 */
	public static function loginDuration()
	{
		if(isset(self::$_loginDuration)) {
			return self::$_loginDuration;
		}

		return self::getSettingsItem(
			self::$_loginDuration,
			self::usersSettings(),
			self::LOGIN_DURATION,
			self::DEFAULT_LOGIN_DURATION
		);
	}

	/**
	 * Get loginRedirect setting -
	 * @return boolean
	 * @default null
	 */
	public static function loginRedirect()
	{
		if(isset(self::$_loginRedirect)) {
			return self::$_loginRedirect;
		}

		return self::getSettingsItem(
			self::$_loginRedirect,
			self::usersSettings(),
			self::LOGIN_REDIRECT,
			self::DEFAULT_LOGIN_REDIRECT
		);
	}

	/**
	 * Get logoutRedirect setting -
	 * @return boolean
	 * @default null
	 */
	public static function logoutRedirect()
	{
		if(isset(self::$_logoutRedirect)) {
			return self::$_logoutRedirect;
		}

		return self::getSettingsItem(
			self::$_logoutRedirect,
			self::usersSettings(),
			self::LOGOUT_REDIRECT,
			self::DEFAULT_LOGOUT_REDIRECT
		);
	}

	/**
	 * Get emailConfirmation setting -
	 * @return boolean
	 * @default true
	 */
	public static function emailConfirmation()
	{
		if(isset(self::$_emailConfirmation)) {
			return self::$_emailConfirmation;
		}

		return self::getSettingsItem(
			self::$_emailConfirmation,
			self::usersSettings(),
			self::EMAIL_CONFIRMATION,
			self::DEFAULT_EMAIL_CONFIRMATION
		);
	}

	/**
	 * Get emailChangeConfirmation setting -
	 * @return boolean
	 * @default true
	 */
	public static function emailChangeConfirmation()
	{
		if(isset(self::$_emailChangeConfirmation)) {
			return self::$_emailChangeConfirmation;
		}

		return self::getSettingsItem(
			self::$_emailChangeConfirmation,
			self::usersSettings(),
			self::EMAIL_CHANGE_CONFIRMATION,
			self::DEFAULT_EMAIL_CHANGE_CONFIRMATION
		);
	}

	/**
	 * Get resetExpireTime setting -
	 * @return boolean
	 * @default '2 days'
	 */
	public static function resetExpireTime()
	{
		if(isset(self::$_resetExpireTime)) {
			return self::$_resetExpireTime;
		}

		return self::getSettingsItem(
			self::$_resetExpireTime,
			self::usersSettings(),
			self::RESET_EXPIRE_TIME,
			self::DEFAULT_RESET_EXPIRE_TIME
		);
	}

	/**
	 * Get loginExpireTime setting -
	 * @return boolean
	 * @default '15 minutes'
	 */
	public static function loginExpireTime()
	{
		if(isset(self::$_loginExpireTime)) {
			return self::$_loginExpireTime;
		}

		return self::getSettingsItem(
			self::$_loginExpireTime,
			self::usersSettings(),
			self::LOGIN_EXPIRE_TIME,
			self::DEFAULT_LOGIN_EXPIRE_TIME
		);
	}

	/**
	 * Get emailViewPath setting -
	 * @return boolean
	 * @default '@user/mail'
	 */
	public static function emailViewPath()
	{
		if(isset(self::$_emailViewPath)) {
			return self::$_emailViewPath;
		}

		return self::getSettingsItem(
			self::$_emailViewPath,
			self::usersSettings(),
			self::EMAIL_VIEW_PATH,
			self::DEFAULT_EMAIL_VIEW_PATH
		);
	}

	/**
	 * Get forceTranslation setting -
	 * @return boolean
	 * @default false
	 */
	public static function forceTranslation()
	{
		if(isset(self::$_forceTranslation)) {
			return self::$_forceTranslation;
		}

		return self::getSettingsItem(
			self::$_forceTranslation,
			self::usersSettings(),
			self::FORCE_TRANSLATION,
			self::DEFAULT_FORCE_TRANSLATION
		);
	}

	/**
	 * Get modelClasses setting -
	 * @return boolean
	 * @default []
	 */
	public static function modelClasses()
	{
		if(isset(self::$_modelClasses)) {
			return self::$_modelClasses;
		}

		return self::getSettingsItem(
			self::$_modelClasses,
			self::usersSettings(),
			self::MODEL_CLASSES,
			self::DEFAULT_MODEL_CLASSES
		);
	}
}
