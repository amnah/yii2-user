<?php

namespace amnah\yii2\user\models\forms;

use Yii;
use yii\base\Model;

/**
 * Forgot password form
 */
class ResendForm extends Model {

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
            ["email", "validateEmailInactive"],
            ["email", "filter", "filter" => "trim"],
        ];
    }

    /**
     * Validate email exists and set user property
     */
    public function validateEmailInactive() {

        // check for valid user
        $user = $this->getUser();
        if (!$user) {
            $this->addError("email", "Email not found");
        }
        elseif ($user->status == $user::STATUS_ACTIVE) {
            $this->addError("email", "Email is already active");
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
            $this->_user = $user::find()
                ->where(["email" => $this->email])
                ->orWhere(["new_email" => $this->email])
                ->one();
        }
        return $this->_user;
    }

    /**
     * Send forgot email
     *
     * @return bool
     */
    public function sendEmail() {

        // validate
        if ($this->validate()) {

            // get user
            /** @var \amnah\yii2\user\models\Userkey $userkey */
            $user = $this->getUser();

            // calculate type
            if ($user->status == $user::STATUS_INACTIVE) {
                $type = $userkey::TYPE_EMAIL_ACTIVATE;
            }
            //elseif ($user->status == $user::STATUS_UNCONFIRMED_EMAIL) {
            else {
                $type = $userkey::TYPE_EMAIL_CHANGE;
            }

            // generate userkey
            $userkey = Yii::$app->getModule("user")->model("Userkey");
            $userkey = $userkey::generate($user->id, $type);

            // send email confirmation
            return $user->sendEmailConfirmation($userkey);
        }

        return false;
    }
}