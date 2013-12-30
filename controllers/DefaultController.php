<?php

namespace amnah\yii2\user\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\AccessControl;
use yii\widgets\ActiveForm;

/**
 * Default controller for User module
 */
class DefaultController extends Controller {

    /**
     * @var \amnah\yii2\user\Module
     */
    protected $_userModule = false;

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
     * @param \amnah\yii2\user\Module $value
     */
    public function setUserModule($value) {
        $this->_userModule = $value;
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
        if (defined('YII_DEBUG')) {
            $actions = $this->getUserModule()->getActions();
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
        $model = $this->getUserModule()->model("LoginForm");
        if ($model->load($_POST) && $model->login($this->getUserModule()->loginDuration)) {
            return $this->goBack(["/user"]);
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
        return $this->goHome();
    }

    /**
     * Display register page
     */
    public function actionRegister() {

        // set up user/profile and attempt to load data from $_POST
        /** @var \amnah\yii2\user\models\User $user */
        /** @var \amnah\yii2\user\models\Profile $profile */
        $user = $this->getUserModule()->model("User", ["scenario" => "register"]);
        $profile = $this->getUserModule()->model("Profile");
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
                $role = $this->getUserModule()->model("Role");
                $user->register($role::ROLE_USER);
                $profile->register($user->id);
                $this->_calcEmailOrLogin($user);

                // set flash
                Yii::$app->session->setFlash("Register-success", $user->getDisplayName());
            }
        }

        // render view
        return $this->render("register", [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Calculate whether we need to send confirmation email or log user in
     *
     * @param \amnah\yii2\user\models\User $user
     */
    protected function _calcEmailOrLogin($user) {

        // determine userkey type to see if we need to send email
        /** @var \amnah\yii2\user\models\User $user */
        /** @var \amnah\yii2\user\models\Userkey $userkey */
        $userkeyType = null;
        $userkey = $this->getUserModule()->model("Userkey");
        if ($user->status == $user::STATUS_INACTIVE) {
            $userkeyType = $userkey::TYPE_EMAIL_ACTIVATE;
        }
        elseif ($user->status == $user::STATUS_UNCONFIRMED_EMAIL) {
            $userkeyType = $userkey::TYPE_EMAIL_CHANGE;
        }

        // check if we have a userkey type to process
        if ($userkeyType !== null) {

            // generate userkey and send email
            $userkey = $userkey::generate($user->id, $userkeyType);
            if (!$numSent = $user->sendEmailConfirmation($userkey)) {

                // handle email error
                //Yii::$app->session->setFlash("Email-error");
            }
        }
        // log user in automatically
        else {
            Yii::$app->user->login($user, $this->getUserModule()->loginDuration);
        }
    }

    /**
     * Confirm email
     */
    public function actionConfirm($key = "") {

        // search for userkey
        /** @var \amnah\yii2\user\models\Userkey $userkey */
        $userkey = $this->getUserModule()->model("Userkey");
        $userkey = $userkey::findActiveByKey($key, [$userkey::TYPE_EMAIL_ACTIVATE, $userkey::TYPE_EMAIL_CHANGE]);
        if ($userkey) {

            // confirm user
            /** @var \amnah\yii2\user\models\User $user */
            $user = $this->getUserModule()->model("User");
            $user = $user::find($userkey->user_id);
            $user->confirm();

            // consume userkey
            $userkey->consume();

            // set flash and refresh
            Yii::$app->session->setFlash("Confirm-success", $user->email);
            $this->refresh();
        }

        // render view
        return $this->render("confirm", [
            "userkey" => $userkey,
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

                // generate userkey and send email if user changed his email
                if ($this->getUserModule()->emailChangeConfirmation and $user->checkAndPrepareEmailChange()) {

                    /** @var \amnah\yii2\user\models\Userkey $userkey */
                    $userkey = $this->getUserModule()->model("Userkey");
                    $userkey = $userkey::generate($user->id, $userkey::TYPE_EMAIL_CHANGE);
                    if (!$numSent = $user->sendEmailConfirmation($userkey)) {

                        // handle email error
                        //Yii::$app->session->setFlash("Email-error");
                    }
                }

                // save, set flash, and refresh page
                $user->save(false);
                Yii::$app->session->setFlash("Account-success", true);
                $this->refresh();
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
        /** @var \amnah\yii2\user\models\User $user */
        /** @var \amnah\yii2\user\models\Profile $profile */
        $user = Yii::$app->user->identity;
        $profile = $user->profile;
        if ($profile->load($_POST)) {

            // validate for ajax request
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($profile);
            }

            // validate for normal request
            if ($profile->validate()) {

                // call something here if needed

                // save - pass false in so that we don't have to validate again
                $profile->save(false);
                Yii::$app->session->setFlash("Profile-success", true);
                $this->refresh();
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
        $model = $this->getUserModule()->model("ResendForm");
        if ($model->load($_POST) && $model->sendEmail()) {

            // set flash and refresh page
            Yii::$app->session->setFlash('Resend-success');
            return $this->refresh();
        }

        // render view
        return $this->render('resend', [
            'model' => $model,
        ]);
    }

    /**
     * Resend email change confirmation
     */
    public function actionResendChange() {

        // attempt to find userkey and get user/profile to send confirmation email
        /** @var \amnah\yii2\user\models\Userkey $userkey */
        $userkey = $this->getUserModule()->model("Userkey");
        $userkey = $userkey::findActiveByUser(Yii::$app->user->id, $userkey::TYPE_EMAIL_CHANGE);
        if ($userkey) {
            /** @var \amnah\yii2\user\models\User $user */
            $user = Yii::$app->user->identity;
            $user->sendEmailConfirmation($userkey);

            // set flash message
            Yii::$app->session->setFlash("Resend-success", true);
        }

        // go to account page
        return $this->redirect(["/user/account"]);
    }

    /**
     * Cancel email change
     */
    public function actionCancel() {

        // attempt to find userkey
        /** @var \amnah\yii2\user\models\Userkey $userkey */
        $userkey = $this->getUserModule()->model("Userkey");
        $userkey = $userkey::findActiveByUser(Yii::$app->user->id, $userkey::TYPE_EMAIL_CHANGE);
        if ($userkey) {

            // remove user.new_email
            /** @var \amnah\yii2\user\models\User $user */
            $user = Yii::$app->user->identity;
            $user->new_email = null;
            $user->save(false);

            // delete userkey and set flash message
            $userkey->expire();
            Yii::$app->session->setFlash("Cancel-success", true);
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
        $model = $this->getUserModule()->model("ForgotForm");
        if ($model->load($_POST) && $model->sendForgotEmail()) {

            // set flash and refresh page
            Yii::$app->session->setFlash('Forgot-success');
            return $this->refresh();
        }

        // render view
        return $this->render('forgot', [
            'model' => $model,
        ]);
    }

    /**
     * Reset password
     */
    public function actionReset($key) {

        // check for success or invalid userkey
        /** @var \amnah\yii2\user\models\Userkey $userkey */
        $success = Yii::$app->session->getFlash('Reset-success');
        $userkey = $this->getUserModule()->model("Userkey");
        $userkey = $userkey::findActiveByKey($key, $userkey::TYPE_PASSWORD_RESET);
        $invalidKey = !$userkey;
        if ($success or $invalidKey) {

            // render view with invalid flag
            // using setFlash()/refresh() would cause an infinite loop
            return $this->render('reset', compact("success", "invalidKey"));
        }

        // attempt to load $_POST data, validate, and reset user password
        /** @var \amnah\yii2\user\models\forms\ResetForm $model */
        $model = $this->getUserModule()->model("ResetForm", ["userkey" => $userkey]);
        if ($model->load($_POST) && $model->resetPassword()) {

            // set flash and refresh page
            Yii::$app->session->setFlash('Reset-success');
            return $this->refresh();
        }

        // render view
        return $this->render('reset', [
            'model' => $model,
        ]);
    }
}