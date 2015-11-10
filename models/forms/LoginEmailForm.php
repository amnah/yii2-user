<?php

namespace amnah\yii2\user\models\forms;

use Yii;
use yii\swiftmailer\Mailer;
use yii\swiftmailer\Message;

/**
 * Login Email Form
 */
class LoginEmailForm extends LoginForm
{
    /**
     * @var string Email
     */
    public $email;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ["email", "required"],
            ["email", "email"],
            ["email", "validateUser"],
            ["rememberMe", "boolean"],
        ];
    }

    /**
     * Validate user
     */
    public function validateUser()
    {
        // set username so we can find the user
        // if found, check for ban status
        $this->username = $this->email;
        $user = $this->getUser();
        if ($user && $user->banned_at) {
            $this->addError("email", Yii::t("user", "User is banned - {banReason}", [
                "banReason" => $user->banned_reason,
            ]));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels["email"] = Yii::t("user", "Email");
        return $labels;
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
        $data = $user ? null : $this->email;

        // calculate expireTime
        $expireTime = $this->module->loginExpireTime;
        $expireTime = $expireTime ? gmdate("Y-m-d H:i:s", strtotime($expireTime)) : null;

        // create userToken
        $userToken = $this->module->model("UserToken");
        $userToken = $userToken::generate($userId, $userToken::TYPE_EMAIL_LOGIN, $data, $expireTime);

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