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
class ResendForm extends Model {

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
        elseif ($user->status == User::STATUS_ACTIVE) {
            $this->addError("email", "Email is already active");
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
            $this->_user = User::find()
                ->where(["email" => $this->email])
                ->orWhere(["new_email" => $this->email])
                ->one();
        }

        // return stored user
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

            // generate a userkey
            $user = $this->getUser();

            // generate userkey
            if ($user->status == User::STATUS_INACTIVE) {
                $type = Userkey::TYPE_EMAIL_ACTIVATE;
            }
            elseif ($user->status == User::STATUS_UNCONFIRMED_EMAIL) {
                $type = Userkey::TYPE_EMAIL_CHANGE;
            }
            $userkey = Userkey::generate($user->id, $type);

            // send email confirmation
            return $user->sendEmailConfirmation($userkey);
        }

        return false;
    }
}