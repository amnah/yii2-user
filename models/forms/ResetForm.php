<?php

namespace amnah\yii2\user\models\forms;

use Yii;
use yii\base\Model;
use amnah\yii2\user\models\User;
use amnah\yii2\user\models\Userkey;

/**
 * Reset password form
 */
class ResetForm extends Model {

    /**
     * @var Userkey
     */
    public $userkey;

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
     * @var User
     */
    protected $_user = false;

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
     * Add user rules
     *
     * @param $rules
     * @return array
     */
    protected function _addUserRules($rules) {

        // go through user rules
        $user = new User;
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
     */
    public function validateUserkeyEmail() {

        // compare user's email
        $user = $this->getUser();
        if (!$user or ($user->email !== $this->email)) {
            $this->addError("email", "Incorrect email");
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
            $this->_user = User::find($this->userkey->user_id);
        }

        // return stored user
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

            // consume userkey
            $userkey = $this->userkey;
            $userkey->consume();

            return true;
        }

        return false;
    }
}