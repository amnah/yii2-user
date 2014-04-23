<?php

namespace amnah\yii2\user\models\forms;

use Yii;
use yii\base\Model;

/**
 * Reset password form
 */
class ResetForm extends Model {

    /**
     * @var \amnah\yii2\user\models\UserKey
     */
    public $userKey;

    /**
     * @var string
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
     * @return array the validation rules.
     */
    public function rules() {

        // set initial rules
        $rules = [
            // uncomment these lines if you want users to confirm their email address
            /*
            [["email"], "required"],
            [["email"], "email"],
            [["email"], "validateUserKeyEmail"],
            [["email"], "filter", "filter" => "trim"],
            */
            [["newPassword", "newPasswordConfirm"], "required"],
            [["newPasswordConfirm"], "compare", "compareAttribute" => "newPassword", "message" => "Passwords do not match"]
        ];

        // add and return user rules
        return $this->_copyNewPasswordRules($rules);
    }

    /**
     * Copy newPassword rules (min length, max length, etc) from user class
     *
     * @param $rules
     * @return array
     */
    protected function _copyNewPasswordRules($rules) {

        // go through user rules
        $user = Yii::$app->getModule("user")->model("User");
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
    public function validateUserKeyEmail() {

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
     * Get user based on userKey.user_id
     *
     * @return \amnah\yii2\user\models\User|null
     */
    public function getUser() {
        if ($this->_user === false) {
            $user = Yii::$app->getModule("user")->model("User");
            $this->_user = $user::findOne($this->userKey->user_id);
        }
        return $this->_user;
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

            // consume userKey
            $userKey = $this->userKey;
            $userKey->consume();

            return true;
        }

        return false;
    }
}