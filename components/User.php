<?php

namespace amnah\yii2\user\components;

use Yii;

/**
 * User component
 */
class User extends \yii\web\User
{
    /**
     * @inheritdoc
     */
    public $identityClass = 'amnah\yii2\user\models\User';

    /**
     * @inheritdoc
     */
    public $enableAutoLogin = true;

    /**
     * @inheritdoc
     */
    public $loginUrl = ["/user/login"];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // check if user is banned. if so, log user out and redirect home
        /** @var \amnah\yii2\user\models\User $user */
        $user = $this->getIdentity();
        if ($user && $user->ban_time) {
            $this->logout();
            Yii::$app->getResponse()->redirect(['/'])->send();
            return;
        }
    }

    /**
     * Check if user is logged in
     *
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
        /** @var \amnah\yii2\user\models\User $identity */
        $identity->updateLoginMeta();
        parent::afterLogin($identity, $cookieBased, $duration);
    }

    /**
     * Get user's display name
     *
     * @param string $default
     * @return string
     */
    public function getDisplayName($default = "")
    {
        /** @var \amnah\yii2\user\models\User $user */
        $user = $this->getIdentity();
        return $user ? $user->getDisplayName($default) : "";
    }

    /**
     * Check if user can do $permissionName.
     * If "authManager" component is set, this will simply use the default functionality.
     * Otherwise, it will use our custom permission system
     *
     * @param string $permissionName
     * @param array  $params
     * @param bool   $allowCaching
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
        /** @var \amnah\yii2\user\models\User $user */
        $user = $this->getIdentity();
        return $user ? $user->can($permissionName) : false;
    }
}
