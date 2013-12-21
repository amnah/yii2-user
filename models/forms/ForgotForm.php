<?php

namespace amnah\yii2\user\models\forms;

use Yii;
use yii\base\Model;
use yii\swiftmailer\Mailer;
use amnah\yii2\user\models\User;
use amnah\yii2\user\models\Userkey;

/**
 * Forgot password form
 */
class ForgotForm extends Model {

    /**
     * @var string Username and/or email
     */
    public $email;

    /**
     * @var User
     */
    protected $_user = false;

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
     * @return User|null
     */
    public function getUser() {

        // check if we need to get user
        if ($this->_user === false) {

            // get user
            $this->_user = User::find(["email" => $this->email]);
        }

        // return stored user
        return $this->_user;
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
            $user = $this->getUser();
            $expireTime = Yii::$app->getModule("user")->resetKeyExpiration
                ? date("Y-m-d H:i:s", strtotime("+" . Yii::$app->getModule("user")->resetKeyExpiration))
                : null;
            $userkey    = Userkey::generate($user->id, Userkey::TYPE_PASSWORD_RESET, $expireTime);

            // modify view path to module views
            /** @var Mailer $mailer */
            $mailer = Yii::$app->mail;
            $mailer->viewPath = Yii::$app->getModule("user")->emailViewPath;

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