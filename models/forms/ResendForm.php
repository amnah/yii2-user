<?php

namespace amnah\yii2\user\models\forms;

use Yii;
use yii\base\Model;

/**
 * Forgot password form
 */
class ResendForm extends Model
{
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
    public function rules()
    {
        return [
            ["email", "required"],
            ["email", "email"],
            ["email", "validateEmailInactive"],
            ["email", "filter", "filter" => "trim"],
        ];
    }

    /**
     * Validate email exists and set user property
     */
    public function validateEmailInactive()
    {
        // check for valid user
        $user = $this->getUser();
        if (!$user) {
            $this->addError("email", Yii::t("user", "Email not found"));
        } elseif ($user->status == $user::STATUS_ACTIVE) {
            $this->addError("email", Yii::t("user", "Email is already active"));
        } else {
            $this->_user = $user;
        }
    }

    /**
     * Get user based on email
     * @return \amnah\yii2\user\models\User|null
     */
    public function getUser()
    {
        // get and store user
        if ($this->_user === false) {
            $user = Yii::$app->getModule("user")->model("User");

            // check email first, then new_email (former is indexed, latter is not)
            $this->_user = $user::findOne(["email" => $this->email]);
            if (!$this->_user) {
                $this->_user = $user::findOne(["new_email" => $this->email]);
            }
        }
        return $this->_user;
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
        if (!$this->validate()) {
            return false;
        }

        /** @var \amnah\yii2\user\models\UserToken $userToken */
        $user = $this->getUser();
        $userToken = Yii::$app->getModule("user")->model("UserToken");

        // calculate type based on user status
        if ($user->status == $user::STATUS_INACTIVE) {
            $type = $userToken::TYPE_EMAIL_ACTIVATE;
        } //elseif ($user->status == $user::STATUS_UNCONFIRMED_EMAIL) {
        else {
            $type = $userToken::TYPE_EMAIL_CHANGE;
        }

        // generate userToken and send email confirmation
        $userToken = $userToken::generate($user->id, $type);
        return $user->sendEmailConfirmation($userToken);
    }
}