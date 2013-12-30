<?php

namespace amnah\yii2\user\models\forms;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model {

    /**
     * @var string Username and/or email
     */
    public $username;

    /**
     * @var string Password
     */
    public $password;

    /**
     * @var bool If true, users will be logged in for $loginDuration
     */
    public $rememberMe = true;

    /**
     * @var \amnah\yii2\user\models\User
     */
    protected $_user = false;

    /**
     * @var \amnah\yii2\user\Module
     */
    protected $_userModule = false;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [["username", "password"], "required"],
            ["username", "validateUser"],
            ["username", "validateUserStatus"],
            ["password", "validatePassword"],
            ["rememberMe", "boolean"],
        ];
    }

    /**
     * Validate user
     */
    public function validateUser() {

        // check for valid user
        $user = $this->getUser();
        if (!$user) {

            // calculate error message
            if ($this->getUserModule()->loginEmail and $this->getUserModule()->loginUsername) {
                $errorAttribute = "Email/username";
            }
            elseif ($this->getUserModule()->loginEmail) {
                $errorAttribute = "Email";
            }
            else {
                $errorAttribute = "Username";
            }
            $this->addError("username", "$errorAttribute not found");
        }
    }

    /**
     * Validate user status
     */
    public function validateUserStatus() {

        // define variables
        /** @var \amnah\yii2\user\models\Userkey $userkey */
        $user = $this->getUser();
        $userkey = $this->getUserModule()->model("Userkey");

        // check for ban status
        if ($user->ban_time) {
            $this->addError("username", "User is banned - {$user->ban_reason}");
        }
        // check for inactive status
        if ($user->status == $user::STATUS_INACTIVE) {
            $userkey = $userkey::generate($user->id, $userkey::TYPE_EMAIL_ACTIVATE);
            $user->sendEmailConfirmation($userkey);
            $this->addError("username", "Email confirmation resent");
        }
    }

    /**
     * Validate password
     */
    public function validatePassword() {

        // skip if there are already errors
        if ($this->hasErrors()) {
            return;
        }

        // check password
        /** @var \amnah\yii2\user\models\User $user */
        $user = $this->getUser();
        if (!$user->verifyPassword($this->password)) {
            $this->addError("password", "Password incorrect");
        }
    }

    /**
     * Get user based on email and/or username
     *
     * @return \amnah\yii2\user\models\User|null
     */
    public function getUser() {

        // check if we need to get user
        if ($this->_user === false) {

            // build query based on email and/or username login properties
            $user = $this->getUserModule()->model("User");
            $user = $user::find();
            if ($this->getUserModule()->loginEmail) {
                $user->orWhere(["email" => $this->username]);
            }
            if ($this->getUserModule()->loginUsername) {
                $user->orWhere(["username" => $this->username]);
            }

            // get and store user
            $this->_user = $user->one();
        }

        // return stored user
        return $this->_user;
    }

    /**
     * Get user module
     *
     * @return \amnah\yii2\user\Module|null
     */
    public function getUserModule() {
        if ($this->_userModule === false) {
            $this->_userModule = Yii::$app->getModule("user");
        }
        return $this->_userModule;
    }

    /**
     * Set user module
     *
     * @param \amnah\yii2\user\Module $value
     */
    public function setUserModule($value) {
        $this->_userModule = $value;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {

        // calculate attribute label for "username"
        $attribute = $this->getUserModule()->requireEmail ? "Email" : "Username";
        return [
            "username" => $attribute,
        ];
    }

    /**
     * Validate and log user in
     *
     * @param int $loginDuration
     * @return bool
     */
    public function login($loginDuration) {

        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? $loginDuration : 0);
        }

        return false;
    }
}