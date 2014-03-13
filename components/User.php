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
        return parent::afterLogin($identity, $cookieBased, $duration);
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
     * Check if user can do $permission
     *
     * @param string $permission
     * @return bool
     */
    public function can($permission) {

        // check for current user and permission
        $user = $this->getIdentity();
        return $user ? $user->can($permission) : false;
    }
}
