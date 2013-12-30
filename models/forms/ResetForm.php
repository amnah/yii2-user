<?php

namespace amnah\yii2\user\models\forms;

use Yii;
use yii\base\Model;

/**
 * Reset password form
 */
class ResetForm extends Model {

    /**
     * @var \amnah\yii2\user\models\Userkey
     */
    public $userkey;

    /**
     * @var string
     * @deprecated
     */
    public $email;

    /**
     * @var string
     */
    public $newPassword;

    /**
     * @var string
     */
    public $newPasswordConfirm;

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

        // set initial rules
        $rules = [
//            [["email"], "required"],
//            [["email"], "email"],
//            [["email"], "validateUserkeyEmail"],
//            [["email"], "filter", "filter" => "trim"],
            [["newPassword", "newPasswordConfirm"], "required"],
            [["newPasswordConfirm"], "compare", "compareAttribute" => "newPassword", "message" => "Passwords do not match"]
        ];

        // add and return user rules
        return $this->_addUserRules($rules);
    }

    /**
     * Add user rules to the newPassword field (min length, max length, etc)
     *
     * @param $rules
     * @return array
     */
    protected function _addUserRules($rules) {

        // go through user rules
        $user = $this->getUserModule()->model("User");
        $userRules = $user->rules();
        foreach ($userRules as $rule) {

            // get first and second elements
            $attribute = $rule[0];
            $validator = trim(strtolower($rule[1]));

            // convert string to array if needed
            if (is_string($attribute)) {
                $attribute = [$attribute];
            }

            // check for newPassword attribute and that it's not required
            if (in_array("newPassword", $attribute) and $validator != "required") {

                // overwrite the attribute
                $rule[0] = ["newPassword"];

                // add to rules
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    /**
     * Validate proper email
     *
     * @deprecated
     */
    public function validateUserkeyEmail() {

        // compare user's email
        $user = $this->getUser();
        if (!$user or ($user->email !== $this->email)) {
            $this->addError("email", "Incorrect email");
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            "newPassword" => "New Password",
            "newPasswordConfirm" => "Confirm New Password",
        ];
    }

    /**
     * Get user based on userkey.user_id
     *
     * @return \amnah\yii2\user\models\User|null
     */
    public function getUser() {
        if ($this->_user === false) {
            $user = $this->getUserModule()->model("User");
            $this->_user = $user::find($this->userkey->user_id);
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
     * Reset user's password
     *
     * @return bool
     */
    public function resetPassword() {

        // validate
        if ($this->validate()) {

            // update password
            $user = $this->getUser();
            $user->newPassword = $this->newPassword;
            $user->save(false);

            // consume userkey
            $userkey = $this->userkey;
            $userkey->consume();

            return true;
        }

        return false;
    }
}