<?php

namespace amnah\yii2\user\components;

use Yii;

/**
 * User component
 */
class User extends \yii\web\User {

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
     * Check if user is logged in
     *
     * @return bool
     */
    public function getIsLoggedIn() {
        return !$this->getIsGuest();
    }

    /**
     * @inheritdoc
     */
    public function afterLogin($identity, $cookieBased, $duration) {
        $identity->setLoginIpAndTime();
        parent::afterLogin($identity, $cookieBased, $duration);
    }

    /**
     * Get user's display name
     *
     * @param string $default
     * @return string
     */
    public function getDisplayName($default = "") {

        // check for current user
        $user = $this->getIdentity();
        return $user ? $user->getDisplayName($default) : "";
    }

    /**
     * Check if user can do $permissionName
     *
     * @param string $permissionName
     * @param array  $params
     * @param bool   $allowCaching
     * @return bool
     */
    public function can($permissionName, $params = [], $allowCaching = true) {

        // check if we have an authmanager. if so, call the parent functionality
        $auth = Yii::$app->getAuthManager();
        if ($auth) {
            return parent::can($permissionName, $params, $allowCaching);
        }

        // otherwise use our own custom permission via roles table
        $user = $this->getIdentity();
        return $user ? $user->can($permissionName) : false;
    }
}
