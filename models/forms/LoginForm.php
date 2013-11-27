<?php

namespace amnah\yii2\user\models\forms;

use Yii;
use yii\base\Model;
use amnah\yii2\user\Module;
use amnah\yii2\user\models\User;

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
     * @var User
     */
    protected $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [["username", "password"], "required"],
            ["username", "validateUser"],
            ["password", "validatePassword", "skipOnError" => true],
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
     * Validate password
     */
    public function validatePassword() {

        // skip if there are already errors
        if ($this->hasErrors()) {
            return;
        }

        // check password
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->verifyPassword($this->password)) {
            $this->addError("password", "Password incorrect");
        }
    }

    /**
     * Get user based on email and/or username
     *
     * @return User|null
     */
    public function getUser() {

        // check if we need to get user
        if ($this->_user === false) {

            // build query based on email and/or username login properties
            $user = User::find();
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
     * Get user module. This is used for accessing the module properties
     *
     * @param string $moduleId
     * @return Module
     */
    public function getUserModule($moduleId = "user") {
        return Yii::$app->getModule($moduleId);
    }
}