<?php

namespace faro\core\user\models\forms;

use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\swiftmailer\Mailer;
use yii\swiftmailer\Message;

/**
 * Forgot password form
 */
class ForgotForm extends Model
{
    /**
     * @var string Username and/or email
     */
    public $email;

    /**
     * @var \faro\core\user\models\User
     */
    protected $user = false;

    /**
     * @var \faro\core\user\Module
     */
    public $module;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!$this->module) {
            $this->module = Yii::$app->getModule("user");
        }
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
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
    public function validateEmail()
    {
        // check for valid user
        $this->user = $this->getUser();
        if (!$this->user) {
            $this->addError("email", Yii::t("user", "Email not found"));
        }
    }

    /**
     * Get user based on email
     * @return \faro\core\user\models\User|null
     */
    public function getUser()
    {
        // get and store user
        if ($this->user === false) {
            $user = $this->module->model("User");
            $this->user = $user::findOne(["email" => $this->email]);
        }
        return $this->user;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            "email" => Yii::t("user", "Email"),
        ];
    }

    /**
     * Send forgot email
     * @return bool
     */
    public function sendForgotEmail()
    {
        /** @var Mailer $mailer */
        /** @var Message $message */
        /** @var \faro\core\user\models\UserToken $userToken */

        if ($this->validate()) {

            // get user
            $user = $this->getUser();

            // calculate expireTime
            $expireTime = $this->module->resetExpireTime;
            $expireTime = $expireTime ? gmdate("Y-m-d H:i:s", strtotime($expireTime)) : null;

            // create userToken
            $userToken = $this->module->model("UserToken");
            $userToken = $userToken::generate($user->id, $userToken::TYPE_PASSWORD_RESET, null, $expireTime);

            // modify view path to module views
            $mailer = Yii::$app->mailer;
            $oldViewPath = $mailer->viewPath;
            $mailer->viewPath = $this->module->emailViewPath;

            // send email
            $subject = Yii::$app->id . " - " . Yii::t("user", "Forgot password");
            $result = $mailer->compose('forgotPassword', compact("subject", "user", "userToken"))
                ->setFrom(Yii::$app->params["senderEmail"])
                ->setTo($user->email)
                ->setSubject($subject)
                ->send();
            
            if (!$result) {
                VarDumper::dump("no se pudo");
                VarDumper::dump($mailer->getErrors());
                die();
            }

            // restore view path and return result
            $mailer->viewPath = $oldViewPath;
            return $result;
        }

        return false;
    }
}