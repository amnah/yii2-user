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
 * @class \p2m\users\components\User
 */

namespace p2m\users\components;

use Yii;

/**
 * class p2m\users\components\User component
 */
class User extends \yii\web\User
{
	/**
	 * @inheritdoc
	 */
	public $identityClass = 'p2m\users\models\User';

	/**
	 * @inheritdoc
	 */
	public $enableAutoLogin = true;

	/**
	 * @inheritdoc
	 */
	public $loginUrl = ['/user/login'];

	/**
	 * @inheritdoc
	 */
	public function getIsGuest()
	{
		/** @var \p2m\users\models\User $user */

		// check if user is banned. if so, log user out and redirect home
		// https://github.com/amnah/yii2-user/issues/99
		$user = $this->getIdentity();
		if ($user && $user->banned_at) {
			$this->logout();
			Yii::$app->getResponse()->redirect(['/'])->send();
		}

		return $user === null;
	}

	/**
	 * Check if user is logged in
	 * @return bool
	 */
	public function getIsLoggedIn()
	{
		return !$this->getIsGuest();
	}

	/**
	 * @inheritdoc
	 */
	public function afterLogin($identity, $cookieBased, $duration)
	{
		/** @var \p2m\users\models\User $identity */
		$identity->updateLoginMeta();
		parent::afterLogin($identity, $cookieBased, $duration);
	}

	/**
	 * Get user's display name
	 * @return string
	 */
	public function getDisplayName()
	{
		/** @var \p2m\users\models\User $user */
		$user = $this->getIdentity();
		return $user ? $user->getDisplayName() : "";
	}

	/**
	 * Check if user can do $permissionName.
	 * If "authManager" component is set, this will simply use the default functionality.
	 * Otherwise, it will use our custom permission system
	 * @param string $permissionName
	 * @param array $params
	 * @param bool $allowCaching
	 * @return bool
	 */
	public function can($permissionName, $params = [], $allowCaching = true)
	{
		// check for auth manager to call parent
		$auth = Yii::$app->getAuthManager();
		if ($auth) {
			return parent::can($permissionName, $params, $allowCaching);
		}

		// otherwise use our own custom permission (via the role table)
		/** @var \p2m\users\models\User $user */
		$user = $this->getIdentity();
		return $user ? $user->can($permissionName) : false;
	}
}
