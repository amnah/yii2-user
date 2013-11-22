<?php

namespace amnah\yii2\user\models\forms;

use Yii;
use yii\base\Model;
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
     * @var string
     */
    public $password;

    /**
     * @var bool If true, users will be logged in for $loginDuration
     */
    public $rememberMe = true;

    /**
     * @var bool If true, users can log in by entering their email
     */
    public $loginEmail = true;

    /**
     * @var bool If true, users can log in by entering their username
     */
    public $loginUsername = true;

    /**
     * @var User
     */
    protected $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validateUserAndPassword'],
            ['rememberMe', 'boolean'],
        ];
    }

    /**
     * Validate user and password
     */
    public function validateUserAndPassword() {
        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError('password', 'Invalid credentials');
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
            if ($this->loginEmail) {
                $user->orWhere(["email" => $this->username]);
            }
            if ($this->loginUsername) {
                $user->orWhere(["username" => $this->username]);
            }

            // get and store user
            $this->_user = $user->one();
        }

        // return stored user
        return $this->_user;
    }
}