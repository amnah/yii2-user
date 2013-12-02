<?php

namespace amnah\yii2\user\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\AccessControl;
use yii\widgets\ActiveForm;
use amnah\yii2\user\models\User;
use amnah\yii2\user\models\Profile;
use amnah\yii2\user\models\Role;
use amnah\yii2\user\models\Userkey;
use amnah\yii2\user\models\forms\LoginForm;
use amnah\yii2\user\models\forms\ForgotForm;
use amnah\yii2\user\models\forms\ResetForm;


/**
 * Default controller for User module
 */
class DefaultController extends Controller {

    /**
     * @inheritdoc
     */
//    public $defaultAction = "profile";

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'confirm'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['account', 'profile', 'resend', 'cancel', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'register', 'forgot', 'reset'],
                        'allow' => true,
                        'roles' => ['?', '*'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Display index
     */
    public function actionIndex() {
        return $this->render('index');
    }

    /**
     * Display login page and log user in
     */
    public function actionLogin() {

        // load data from $_POST and attempt login
        $model = new LoginForm();
        if ($model->load($_POST) && $model->login(Yii::$app->getModule("user")->loginDuration)) {
            return $this->goBack();
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
        $user = new User(["scenario" => "register"]);
        $profile = new Profile();
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
                $user->register(Role::USER);
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
     * @param User $user
     */
    protected function _calcEmailOrLogin($user) {

        // determine userkey type to see if we need to send email
        $userkeyType = null;
        if ($user->status == User::STATUS_INACTIVE) {
            $userkeyType = Userkey::TYPE_EMAIL_ACTIVATE;
        }
        elseif ($user->status == User::STATUS_UNCONFIRMED_EMAIL) {
            $userkeyType = Userkey::TYPE_EMAIL_CHANGE;
        }

        // generate userkey and send email
        if ($userkeyType !== null) {
            $userkey = Userkey::generate($user->id, $userkeyType);
            $numSent = $user->sendEmailConfirmation($userkey);
        }
        // log user in automatically
        else {
            Yii::$app->user->login($user, Yii::$app->getModule("user")->loginDuration);
        }
    }

    /**
     * Confirm email
     */
    public function actionConfirm($key = "") {

        // search for userkey
        $userkey = Userkey::findActiveByKey($key, [Userkey::TYPE_EMAIL_ACTIVATE, Userkey::TYPE_EMAIL_CHANGE]);
        if ($userkey) {

            // confirm user
            /** @var User $user */
            $user = User::find($userkey->user_id);
            $user->confirm();

            // consume userkey
            $userkey->consume();

            // set flash and refresh
            Yii::$app->session->setFlash("Confirm-success", true);
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
        /** @var User $user */
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
                if (Yii::$app->getModule("user")->emailChangeConfirmation and $user->checkAndPrepareEmailChange()) {
                    $userkey = Userkey::generate($user->id, Userkey::TYPE_EMAIL_CHANGE);
                    $numSent = $user->sendEmailConfirmation($userkey);
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
        /** @var Profile $profile */
        $profile = Yii::$app->user->identity->profile;
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
     * Resend email change confirmation
     */
    public function actionResend() {

        // attempt to find userkey and get user/profile to send confirmation email
        $userkey = Userkey::findActiveByUser(Yii::$app->user->id, Userkey::TYPE_EMAIL_CHANGE);
        if ($userkey) {
            /** @var User $user */
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
        $userkey = Userkey::findActiveByUser(Yii::$app->user->id, Userkey::TYPE_EMAIL_CHANGE);
        if ($userkey) {

            // remove user.new_email
            /** @var User $user */
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
        $model = new ForgotForm();
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
        $userkey = Userkey::findActiveByKey($key, Userkey::TYPE_PASSWORD_RESET);
        $success = Yii::$app->session->getFlash('Reset-success');
        $invalidKey = !$userkey;
        if ($success or $invalidKey) {

            // render view with invalid flag
            // using setFlash()/refresh() would cause an infinite loop
            return $this->render('reset', compact("success", "invalidKey"));
        }

        // attempt to load $_POST data, validate, and reset user password
        $model = new ResetForm(["userkey" => $userkey]);
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