<?php

namespace amnah\yii2\user\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\AccessControl;
use yii\widgets\ActiveForm;
use yii\swiftmailer\Mailer;
use amnah\yii2\user\models\User;
use amnah\yii2\user\models\Profile;
use amnah\yii2\user\models\Role;
use amnah\yii2\user\models\Userkey;
use amnah\yii2\user\models\forms\LoginForm;


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
                        'actions' => ['login', 'register', 'forgot'],
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

        // load data from $_POST and validate
        $model = new LoginForm();
        if ($model->load($_POST) && $model->validate()) {

            // log in and go back
            Yii::$app->user->login($model->getUser(), $model->rememberMe ?Yii::$app->getModule("user")->loginDuration : 0);
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
        $this->goHome();
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
                $this->_calcEmailOrLogin($user, $profile);

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
     * @param Profile $profile
     */
    protected function _calcEmailOrLogin($user, $profile) {

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
            $numSent = $this->_sendEmailConfirmation($user, $profile, $userkey);
        }
        // log user in automatically
        else {
            Yii::$app->user->switchIdentity($user, Yii::$app->getModule("user")->loginDuration);
        }
    }

    /**
     * Send email confirmation to user
     *
     * @param User $user
     * @param Profile $profile
     * @param Userkey $userkey
     * @return int
     */
    protected function _sendEmailConfirmation($user, $profile, $userkey) {

        // modify view path to module views
        /** @var Mailer $mailer */
        $mailer = Yii::$app->mail;
        $mailer->viewPath = Yii::$app->getModule("user")->alias . "/views/_email";

        // send email
        $subject = Yii::$app->id . " - Email confirmation";
        return  $mailer->compose('confirmEmail', compact("user", "profile", "userkey", "subject"))
            ->setTo($user->email)
            ->setSubject($subject)
            ->send();
    }

    /**
     * Confirm email
     */
    public function actionConfirm($key) {

        // search for userkey
        $success = false;
        if ($userkey = Userkey::findForConfirm($key)) {

            // confirm user
            /** @var User $user */
            $user = User::find($userkey->user_id);
            $user->confirm();

            // consume userkey
            $userkey->consume();

            // set success
            $success = true;
        }

        // render view
        return $this->render("confirm", [
            "userkey" => $userkey,
            "success" => $success,
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
                if ($user->checkAndPrepareEmailChange()) {
                    $userkey = Userkey::generate($user->id, Userkey::TYPE_EMAIL_CHANGE);
                    $numSent = $this->_sendEmailConfirmation($user, $user->profile, $userkey);
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
        if ($userkey = Userkey::findForResend(Yii::$app->user->id, Userkey::TYPE_EMAIL_CHANGE)) {
            $user = Yii::$app->user->identity;
            $profile = $user->profile;
            $this->_sendEmailConfirmation($user, $profile, $userkey);

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
        if ($userkey = Userkey::findForResend(Yii::$app->user->id, Userkey::TYPE_EMAIL_CHANGE)) {

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

    }
}