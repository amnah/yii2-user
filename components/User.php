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
     * Get user's email
     *
     * @return string
     */
    public function getEmail() {
        return $this->getIdentity()->email;
    }

    /**
     * Get user's username
     *
     * @return mixed
     */
    public function getUsername() {
        return $this->getIdentity()->username;
    }

    /**
     * Get user's display name
     *
     * @param string $default
     * @return string
     */
    public function getDisplayName($default = "") {
        return $this->getIdentity()->getDisplayName($default);
    }

    /**
     * Check if user can do $permission
     *
     * @param string $permission
     * @return bool
     */
    public function can($permission) {

        // get current user if not specified
        $user = $this->getIdentity();
        return ($user and $user->can($permission));
    }

    /**
     * Check if user cant do $permission
     *
     * @param string $permission
     * @param bool $user
     * @return bool
     */
    public function cant($permission, $user = false) {
        return !$this->can($permission, $user);
    }
}
