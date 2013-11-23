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
    public $identityClass = "amnah\yii2\user\models\User";

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
     * Get a clean display name for the user
     *
     * @param string $default
     * @return string
     */
    public function getDisplayName($default = "") {

        // define possible names
        $possibleNames = [
            "email",
            "username",
            "id",
        ];

        // go through each and check
        foreach ($possibleNames as $possibleName) {
            if (!empty($this->$possibleName)) {
                return $this->$possibleName;
            }
        }

        // return default
        return $default;
    }
}
