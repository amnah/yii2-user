<?php

namespace amnah\yii2\user\models\forms;

use Yii;
use yii\base\Model;

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
            $user = Yii::$app->getModule("user")->model("User");
            $this->_user = $user::findOne(["email" => $this->email]);
        }
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

            // get user
            /** @var \amnah\yii2\user\models\Userkey $userkey */
            $user = $this->getUser();

            // calculate expireTime (converting strtotime) and create userkey object
            $expireTime = Yii::$app->getModule("user")->resetKeyExpiration;
            $expireTime = $expireTime !== null ? date("Y-m-d H:i:s", strtotime("+" . $expireTime)) : null;
            $userkey = Yii::$app->getModule("user")->model("Userkey");
            $userkey    = $userkey::generate($user->id, $userkey::TYPE_PASSWORD_RESET, $expireTime);

            // modify view path to module views
            $mailer = Yii::$app->mail;
            $oldViewPath = $mailer->viewPath;
            $mailer->viewPath = Yii::$app->getModule("user")->emailViewPath;

            // send email
            $subject = Yii::$app->id . " - Forgot password";
            $result = $mailer->compose('forgotPassword', compact("subject", "user", "userkey"))
                ->setTo($user->email)
                ->setSubject($subject)
                ->send();

            // restore view path and return result
            $mailer->viewPath = $oldViewPath;
            return $result;
        }

        return false;
    }
}