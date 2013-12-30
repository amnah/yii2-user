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
            $user = $this->getUserModule()->model("User");
            $this->_user = $user::find()
                ->where(["email" => $this->email])
                ->orWhere(["new_email" => $this->email])
                ->one();
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
    public function sendEmail() {

        // validate
        if ($this->validate()) {

            // define variables
            /** @var \amnah\yii2\user\models\Userkey $userkey */
            $user = $this->getUser();
            $userkey = $this->getUserModule()->model("Userkey");

            // calculate type and generate userkey
            if ($user->status == $user::STATUS_INACTIVE) {
                $type = $userkey::TYPE_EMAIL_ACTIVATE;
            }
            //elseif ($user->status == $user::STATUS_UNCONFIRMED_EMAIL) {
            else {
                $type = $userkey::TYPE_EMAIL_CHANGE;
            }
            $userkey = $userkey::generate($user->id, $type);

            // send email confirmation
            return $user->sendEmailConfirmation($userkey);
        }

        return false;
    }
}