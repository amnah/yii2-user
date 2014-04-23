<?php

namespace amnah\yii2\user\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;

/**
 * Default controller for User module
 */
class DefaultController extends Controller {

    /**
     * Get view path based on module property
     *
     * @return string
     */
    public function getViewPath() {
        return Yii::$app->getModule("user")->viewPath
            ? rtrim(Yii::$app->getModule("user")->viewPath, "/\\") . DIRECTORY_SEPARATOR . $this->id
            : parent::getViewPath();
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'confirm', 'resend'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['account', 'profile', 'resend-change', 'cancel', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'register', 'forgot', 'reset'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Display index
     */
    public function actionIndex() {

        // display debug page if YII_DEBUG is set
        if (defined('YII_DEBUG') and YII_DEBUG) {
            $actions = Yii::$app->getModule("user")->getActions();
            return $this->render('index', ["actions" => $actions]);
        }
        // redirect to login page if user is guest
        elseif (Yii::$app->user->isGuest) {
            return $this->redirect(["/user/login"]);
        }
        // redirect to account page if user is logged in
        else {
            return $this->redirect(["/user/account"]);
        }
    }

    /**
     * Display login page and log user in
     */
    public function actionLogin() {

        // load data from $_POST and attempt login
        /** @var \amnah\yii2\user\models\forms\LoginForm $model */
        $model = Yii::$app->getModule("user")->model("LoginForm");
        if ($model->load($_POST) && $model->login(Yii::$app->getModule("user")->loginDuration)) {
            return $this->goBack(Yii::$app->getModule("user")->loginRedirect);
        }

        // render view
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Log user out and redirect home
     */
    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->redirect(Yii::$app->getModule("user")->logoutRedirect);
    }

    /**
     * Display register page
     */
    public function actionRegister() {

        // set up user/profile and attempt to load data from $_POST
        /** @var \amnah\yii2\user\models\User $user */
        /** @var \amnah\yii2\user\models\Profile $profile */
        $user = Yii::$app->getModule("user")->model("User", ["scenario" => "register"]);
        $profile = Yii::$app->getModule("user")->model("Profile");
        if ($user->load($_POST)) {

            // validate for ajax request
            $profile->load($_POST);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($user, $profile);
            }

            // validate for normal request
            if ($user->validate() and $profile->validate()) {

                // perform registration
                /** @var \amnah\yii2\user\models\Role $role */
                $role = Yii::$app->getModule("user")->model("Role");
                $user->register($role::ROLE_USER, Yii::$app->request->userIP);
                $profile->register($user->id);
                $this->_calcEmailOrLogin($user);

                // set flash
                // dont use $this->refresh() because user may automatically be logged in and get 403 forbidden
                $userDisplayName = $user->getDisplayName();
                $guestText = Yii::$app->user->isGuest ? " - Please check your email to confirm your account" : "";
                Yii::$app->session->setFlash("Register-success", "Successfully registered [ $userDisplayName ]" . $guestText);
            }
        }

        // render view
        return $this->render("register", [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Calculate whether we need to send confirmation email or log user in based on user's status
     *
     * @param \amnah\yii2\user\models\User $user
     */
    protected function _calcEmailOrLogin($user) {

        // determine userKey type to see if we need to send email
        /** @var \amnah\yii2\user\models\User $user */
        /** @var \amnah\yii2\user\models\UserKey $userKey */
        $userKeyType = null;
        $userKey = Yii::$app->getModule("user")->model("UserKey");
        if ($user->status == $user::STATUS_INACTIVE) {
            $userKeyType = $userKey::TYPE_EMAIL_ACTIVATE;
        }
        elseif ($user->status == $user::STATUS_UNCONFIRMED_EMAIL) {
            $userKeyType = $userKey::TYPE_EMAIL_CHANGE;
        }

        // check if we have a userKey type to process
        if ($userKeyType !== null) {

            // generate userKey and send email
            $userKey = $userKey::generate($user->id, $userKeyType);
            if (!$numSent = $user->sendEmailConfirmation($userKey)) {

                // handle email error
                //Yii::$app->session->setFlash("Email-error", "Failed to send email");
            }
        }
        // log user in automatically
        else {
            Yii::$app->user->login($user, Yii::$app->getModule("user")->loginDuration);
        }
    }

    /**
     * Confirm email
     */
    public function actionConfirm($key) {

        // search for userKey
        /** @var \amnah\yii2\user\models\UserKey $userKey */
        $success = false;
        $userKey = Yii::$app->getModule("user")->model("UserKey");
        $userKey = $userKey::findActiveByKey($key, [$userKey::TYPE_EMAIL_ACTIVATE, $userKey::TYPE_EMAIL_CHANGE]);
        if ($userKey) {

            // confirm user
            /** @var \amnah\yii2\user\models\User $user */
            $user = Yii::$app->getModule("user")->model("User");
            $user = $user::findOne($userKey->user_id);
            $user->confirm();

            // consume userKey and set success
            $userKey->consume();
            $success = $user->email;
        }

        // render view
        return $this->render("confirm", [
            "userKey" => $userKey,
            "success" => $success
        ]);
    }

    /**
     * Account
     */
    public function actionAccount() {

        // set up user/profile and attempt to load data from $_POST
        /** @var \amnah\yii2\user\models\User $user */
        $user = Yii::$app->user->identity;
        $user->setScenario("account");
        if ($user->load($_POST)) {

            // validate for ajax request
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($user);
            }

            // validate for normal request
            if ($user->validate()) {

                // generate userKey and send email if user changed his email
                if (Yii::$app->getModule("user")->emailChangeConfirmation and $user->checkAndPrepareEmailChange()) {

                    /** @var \amnah\yii2\user\models\UserKey $userKey */
                    $userKey = Yii::$app->getModule("user")->model("UserKey");
                    $userKey = $userKey::generate($user->id, $userKey::TYPE_EMAIL_CHANGE);
                    if (!$numSent = $user->sendEmailConfirmation($userKey)) {

                        // handle email error
                        //Yii::$app->session->setFlash("Email-error", "Failed to send email");
                    }
                }

                // save, set flash, and refresh page
                $user->save(false);
                Yii::$app->session->setFlash("Account-success", "Account updated");
                return $this->refresh();
            }
        }

        // render view
        return $this->render("account", [
            'user' => $user,
        ]);
    }

    /**
     * Profile
     */
    public function actionProfile() {

        // set up profile and attempt to load data from $_POST
        /** @var \amnah\yii2\user\models\Profile $profile */
        $profile = Yii::$app->user->identity->profile;
        if ($profile->load($_POST)) {

            // validate for ajax request
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($profile);
            }

            // save
            if ($profile->save()) {
                Yii::$app->session->setFlash("Profile-success", "Profile updated");
                return $this->refresh();
            }
        }

        // render view
        return $this->render("profile", [
            'profile' => $profile,
        ]);
    }

    /**
     * Resend email confirmation
     */
    public function actionResend() {

        // attempt to load $_POST data, validate, and send email
        /** @var \amnah\yii2\user\models\forms\ResendForm $model */
        $model = Yii::$app->getModule("user")->model("ResendForm");
        if ($model->load($_POST) && $model->sendEmail()) {

            // set flash and refresh page
            Yii::$app->session->setFlash("Resend-success", "Confirmation email resent");
            return $this->refresh();
        }

        // render view
        return $this->render("resend", [
            "model" => $model,
        ]);
    }

    /**
     * Resend email change confirmation
     */
    public function actionResendChange() {

        // attempt to find userKey and get user/profile to send confirmation email
        /** @var \amnah\yii2\user\models\UserKey $userKey */
        $userKey = Yii::$app->getModule("user")->model("UserKey");
        $userKey = $userKey::findActiveByUser(Yii::$app->user->id, $userKey::TYPE_EMAIL_CHANGE);
        if ($userKey) {
            /** @var \amnah\yii2\user\models\User $user */
            $user = Yii::$app->user->identity;
            $user->sendEmailConfirmation($userKey);

            // set flash message
            Yii::$app->session->setFlash("Resend-success", "Confirmation email resent");
        }

        // go to account page
        return $this->redirect(["/user/account"]);
    }

    /**
     * Cancel email change
     */
    public function actionCancel() {

        // attempt to find userKey
        /** @var \amnah\yii2\user\models\UserKey $userKey */
        $userKey = Yii::$app->getModule("user")->model("UserKey");
        $userKey = $userKey::findActiveByUser(Yii::$app->user->id, $userKey::TYPE_EMAIL_CHANGE);
        if ($userKey) {

            // remove user.new_email
            /** @var \amnah\yii2\user\models\User $user */
            $user = Yii::$app->user->identity;
            $user->new_email = null;
            $user->save(false);

            // delete userKey and set flash message
            $userKey->expire();
            Yii::$app->session->setFlash("Cancel-success", "Email change cancelled");
        }

        // go to account page
        return $this->redirect(["/user/account"]);
    }

    /**
     * Forgot password
     */
    public function actionForgot() {

        // attempt to load $_POST data, validate, and send email
        /** @var \amnah\yii2\user\models\forms\ForgotForm $model */
        $model = Yii::$app->getModule("user")->model("ForgotForm");
        if ($model->load($_POST) && $model->sendForgotEmail()) {

            // set flash and refresh page
            Yii::$app->session->setFlash("Forgot-success", "Instructions to reset your password have been sent");
            //return $this->refresh();
        }

        // render view
        return $this->render("forgot", [
            "model" => $model,
        ]);
    }

    /**
     * Reset password
     */
    public function actionReset($key) {

        // check for invalid userKey
        /** @var \amnah\yii2\user\models\UserKey $userKey */
        $userKey = Yii::$app->getModule("user")->model("UserKey");
        $userKey = $userKey::findActiveByKey($key, $userKey::TYPE_PASSWORD_RESET);
        if (!$userKey) {
            return $this->render('reset', ["invalidKey" => true]);
        }

        // attempt to load $_POST data, validate, and reset user password
        /** @var \amnah\yii2\user\models\forms\ResetForm $model */
        $success = false;
        $model = Yii::$app->getModule("user")->model("ResetForm", ["userKey" => $userKey]);
        if ($model->load($_POST) && $model->resetPassword()) {
            $success = true;
        }

        // render view
        return $this->render('reset', compact("model", "success"));
    }
}