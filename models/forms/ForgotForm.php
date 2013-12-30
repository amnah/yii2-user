<?php

namespace amnah\yii2\user\models\forms;

use Yii;
use yii\base\Model;
use yii\swiftmailer\Mailer;

/**
 * Forgot password form
 */
class ForgotForm extends Model {

    /**
     * @var string Username and/or email
     */
    public $email;

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
            ["email", "required"],
            ["email", "email"],
            ["email", "validateEmail"],
            ["email", "filter", "filter" => "trim"],
        ];
    }

    /**
     * Validate email exists and set user property
     */
    public function validateEmail() {

        // check for valid user
        $user = $this->getUser();
        if (!$user) {
            $this->addError("email", "Email not found");
        }
        else {
            $this->_user = $user;
        }
    }

    /**
     * Get user based on email
     *
     * @return \amnah\yii2\user\models\User|null
     */
    public function getUser() {
        if ($this->_user === false) {
            $user = $this->getUserModule()->model("User");
            $this->_user = $user::find(["email" => $this->email]);
        }
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
     * Send forgot email
     *
     * @return bool
     */
    public function sendForgotEmail() {

        // validate
        if ($this->validate()) {

            // generate a userkey
            /** @var \amnah\yii2\user\models\Userkey $userkey */
            $user = $this->getUser();
            $userkey = $this->getUserModule()->model("Userkey");
            $expireTime = $this->getUserModule()->resetKeyExpiration;
            $expireTime = $expireTime !== null ? date("Y-m-d H:i:s", strtotime("+" . $expireTime)) : null;
            $userkey    = $userkey::generate($user->id, $userkey::TYPE_PASSWORD_RESET, $expireTime);

            // modify view path to module views
            /** @var Mailer $mailer */
            $mailer = Yii::$app->mail;
            $mailer->viewPath = $this->getUserModule()->emailViewPath;

            // send email
            $subject = Yii::$app->id . " - Forgot password";
            $mailer->compose('forgotPassword', compact("subject", "user", "userkey"))
                ->setTo($user->email)
                ->setSubject($subject)
                ->send();

            return true;
        }

        return false;
    }
}