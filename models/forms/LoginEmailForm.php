<?php

namespace amnah\yii2\user\models\forms;

use Yii;
use yii\base\Model;
use yii\swiftmailer\Mailer;
use yii\swiftmailer\Message;

/**
 * Login Email Form
 */
class LoginEmailForm extends Model
{
    /**
     * @var string Username and/or email
     */
    public $email;

    /**
     * @var \amnah\yii2\user\models\User
     */
    protected $user = false;

    /**
     * @var \amnah\yii2\user\Module
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
            ["email", "filter", "filter" => "trim"],
        ];
    }

    /**
     * Get user based on email
     * @return \amnah\yii2\user\models\User|null
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
    public function sendEmail()
    {
        /** @var Mailer $mailer */
        /** @var Message $message */
        /** @var \amnah\yii2\user\models\UserToken $userToken */

        if (!$this->validate()) {
            return false;
        }

        // get user and calculate userToken info
        $user = $this->getUser();
        $userId = $user ? $user->id : null;
        $email = $user ? null : $this->email;

        // calculate expireTime (converting via strtotime)
        $expireTime = $this->module->loginExpireTime;
        $expireTime = $expireTime ? date("Y-m-d H:i:s", strtotime($expireTime)) : null;

        // create userToken
        $userToken = $this->module->model("UserToken");
        $userToken = $userToken::generate($userId, $userToken::TYPE_EMAIL_LOGIN, $email, $expireTime);

        // modify view path to module views
        $mailer = Yii::$app->mailer;
        $oldViewPath = $mailer->viewPath;
        $mailer->viewPath = $this->module->emailViewPath;

        // send email
        $subject = $user ? "Login" : "Register";
        $subject = Yii::$app->id . " - " . Yii::t("user", $subject);
        $result = $mailer->compose('loginToken', compact("subject", "user", "userToken"))
            ->setTo($this->email)
            ->setSubject($subject)
            ->send();

        // restore view path and return result
        $mailer->viewPath = $oldViewPath;
        return $result;
    }
}