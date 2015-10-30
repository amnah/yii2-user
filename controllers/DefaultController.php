<?php

namespace amnah\yii2\user\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;

/**
 * Default controller for User module
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Display index - debug page, login page, or account page
     */
    public function actionIndex()
    {
        if (defined('YII_DEBUG') && YII_DEBUG) {
            $actions = Yii::$app->getModule("user")->getActions();
            return $this->render('index', ["actions" => $actions]);
        } elseif (Yii::$app->user->isGuest) {
            return $this->redirect(["/user/login"]);
        } else {
            return $this->redirect(["/user/account"]);
        }
    }

    /**
     * Display login page
     */
    public function actionLogin()
    {
        /** @var \amnah\yii2\user\models\forms\LoginForm $model */

        // load post data and login
        $model = Yii::$app->getModule("user")->model("LoginForm");
        if ($model->load(Yii::$app->request->post()) && $model->login(Yii::$app->getModule("user")->loginDuration)) {

            // check for a valid returnUrl (to prevent a weird login bug)
            //   https://github.com/amnah/yii2-user/issues/115
            $loginRedirect = Yii::$app->getModule("user")->loginRedirect;
            $returnUrl = Yii::$app->user->getReturnUrl($loginRedirect);
            if (strpos($returnUrl, "user/login") !== false) {
                $returnUrl = null;
            }
            return $this->redirect($returnUrl);
        }

        // render
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Log user out and redirect
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        // redirect
        $logoutRedirect = Yii::$app->getModule("user")->logoutRedirect;
        if ($logoutRedirect === null) {
            return $this->goHome();
        } else {
            return $this->redirect($logoutRedirect);
        }
    }

    /**
     * Display registration page
     */
    public function actionRegister()
    {
        /** @var \amnah\yii2\user\models\User $user */
        /** @var \amnah\yii2\user\models\Profile $profile */
        /** @var \amnah\yii2\user\models\Role $role */

        // set up new user/profile objects
        $user = Yii::$app->getModule("user")->model("User", ["scenario" => "register"]);
        $profile = Yii::$app->getModule("user")->model("Profile");

        // load post data
        $post = Yii::$app->request->post();
        if ($user->load($post)) {

            // ensure profile data gets loaded
            $profile->load($post);

            // validate for ajax request
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($user, $profile);
            }

            // validate for normal request
            if ($user->validate() && $profile->validate()) {

                // perform registration
                $role = Yii::$app->getModule("user")->model("Role");
                $user->setRegisterAttributes($role::ROLE_USER, Yii::$app->request->userIP)->save(false);
                $profile->setUser($user->id)->save(false);
                $this->afterRegister($user);

                // set flash
                // don't use $this->refresh() because user may automatically be logged in and get 403 forbidden
                $successText = Yii::t("user", "Successfully registered [ {displayName} ]", ["displayName" => $user->getDisplayName()]);
                $guestText = "";
                if (Yii::$app->user->isGuest) {
                    $guestText = Yii::t("user", " - Please check your email to confirm your account");
                }
                Yii::$app->session->setFlash("Register-success", $successText . $guestText);
            }
        }

        // render
        return $this->render("register", [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Process data after registration
     *
     * @param \amnah\yii2\user\models\User $user
     */
    protected function afterRegister($user)
    {
        /** @var \amnah\yii2\user\models\UserToken $userToken */

        // determine userToken type to see if we need to send email
        $userToken = Yii::$app->getModule("user")->model("UserToken");
        if ($user->status == $user::STATUS_INACTIVE) {
            $userTokenType = $userToken::TYPE_EMAIL_ACTIVATE;
        } elseif ($user->status == $user::STATUS_UNCONFIRMED_EMAIL) {
            $userTokenType = $userToken::TYPE_EMAIL_CHANGE;
        } else {
            $userTokenType = null;
        }

        // check if we have a userToken type to process, or just log user in directly
        if ($userTokenType) {

            // generate userToken and send email
            $userToken = $userToken::generate($user->id, $userTokenType);
            if (!$numSent = $user->sendEmailConfirmation($userToken)) {

                // handle email error
                //Yii::$app->session->setFlash("Email-error", "Failed to send email");
            }
        } else {
            Yii::$app->user->login($user, Yii::$app->getModule("user")->loginDuration);
        }
    }

    /**
     * Confirm email
     */
    public function actionConfirm($token)
    {
        /** @var \amnah\yii2\user\models\UserToken $userToken */
        /** @var \amnah\yii2\user\models\User $user */

        // search for userToken
        $success = false;
        $userToken = Yii::$app->getModule("user")->model("UserToken");
        $userToken = $userToken::findByToken($token, [$userToken::TYPE_EMAIL_ACTIVATE, $userToken::TYPE_EMAIL_CHANGE]);
        if ($userToken) {

            // confirm user
            $user = Yii::$app->getModule("user")->model("User");
            $user = $user::findOne($userToken->user_id);
            $user->confirm();

            // delete userToken and set success
            $userToken->delete();
            $success = $user->email;
        }

        // render
        return $this->render("confirm", [
            "userToken" => $userToken,
            "success" => $success
        ]);
    }

    /**
     * Account
     */
    public function actionAccount()
    {
        /** @var \amnah\yii2\user\models\User $user */
        /** @var \amnah\yii2\user\models\UserToken $userToken */

        // set up user and load post data
        $user = Yii::$app->user->identity;
        $user->setScenario("account");
        $loadedPost = $user->load(Yii::$app->request->post());

        // validate for ajax request
        if ($loadedPost && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($user);
        }

        // validate for normal request
        if ($loadedPost && $user->validate()) {

            // generate userToken and send email if user changed his email
            if (Yii::$app->getModule("user")->emailChangeConfirmation && $user->checkAndPrepEmailChange()) {

                $userToken = Yii::$app->getModule("user")->model("UserToken");
                $userToken = $userToken::generate($user->id, $userToken::TYPE_EMAIL_CHANGE);
                if (!$numSent = $user->sendEmailConfirmation($userToken)) {

                    // handle email error
                    //Yii::$app->session->setFlash("Email-error", "Failed to send email");
                }
            }

            // save, set flash, and refresh page
            $user->save(false);
            Yii::$app->session->setFlash("Account-success", Yii::t("user", "Account updated"));
            return $this->refresh();
        }

        // render
        return $this->render("account", [
            'user' => $user,
        ]);
    }

    /**
     * Profile
     */
    public function actionProfile()
    {
        /** @var \amnah\yii2\user\models\Profile $profile */

        // set up profile and load post data
        $profile = Yii::$app->user->identity->profile;
        $loadedPost = $profile->load(Yii::$app->request->post());

        // validate for ajax request
        if ($loadedPost && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($profile);
        }

        // validate for normal request
        if ($loadedPost && $profile->validate()) {
            $profile->save(false);
            Yii::$app->session->setFlash("Profile-success", Yii::t("user", "Profile updated"));
            return $this->refresh();
        }

        // render
        return $this->render("profile", [
            'profile' => $profile,
        ]);
    }

    /**
     * Resend email confirmation
     */
    public function actionResend()
    {
        /** @var \amnah\yii2\user\models\forms\ResendForm $model */

        // load post data and send email
        $model = Yii::$app->getModule("user")->model("ResendForm");
        if ($model->load(Yii::$app->request->post()) && $model->sendEmail()) {

            // set flash (which will show on the current page)
            Yii::$app->session->setFlash("Resend-success", Yii::t("user", "Confirmation email resent"));
        }

        // render
        return $this->render("resend", [
            "model" => $model,
        ]);
    }

    /**
     * Resend email change confirmation
     */
    public function actionResendChange()
    {
        /** @var \amnah\yii2\user\models\User $user */
        /** @var \amnah\yii2\user\models\UserToken $userToken */

        // find userToken of type email change
        $user = Yii::$app->user->identity;
        $userToken = Yii::$app->getModule("user")->model("UserToken");
        $userToken = $userToken::findByUser($user->id, $userToken::TYPE_EMAIL_CHANGE);
        if ($userToken) {

            // send email and set flash message
            $user->sendEmailConfirmation($userToken);
            Yii::$app->session->setFlash("Resend-success", Yii::t("user", "Confirmation email resent"));
        }

        // redirect to account page
        return $this->redirect(["/user/account"]);
    }

    /**
     * Cancel email change
     */
    public function actionCancel()
    {
        /** @var \amnah\yii2\user\models\User $user */
        /** @var \amnah\yii2\user\models\UserToken $userToken */

        // find userToken of type email change
        $user = Yii::$app->user->identity;
        $userToken = Yii::$app->getModule("user")->model("UserToken");
        $userToken = $userToken::findByUser($user->id, $userToken::TYPE_EMAIL_CHANGE);
        if ($userToken) {

            // remove `user.new_email`
            $user->new_email = null;
            $user->save(false);

            // delete userToken and set flash message
            $userToken->delete();
            Yii::$app->session->setFlash("Cancel-success", Yii::t("user", "Email change cancelled"));
        }

        // go to account page
        return $this->redirect(["/user/account"]);
    }

    /**
     * Forgot password
     */
    public function actionForgot()
    {
        /** @var \amnah\yii2\user\models\forms\ForgotForm $model */

        // load post data and send email
        $model = Yii::$app->getModule("user")->model("ForgotForm");
        if ($model->load(Yii::$app->request->post()) && $model->sendForgotEmail()) {

            // set flash (which will show on the current page)
            Yii::$app->session->setFlash("Forgot-success", Yii::t("user", "Instructions to reset your password have been sent"));
        }

        // render
        return $this->render("forgot", [
            "model" => $model,
        ]);
    }

    /**
     * Reset password
     */
    public function actionReset($token)
    {
        /** @var \amnah\yii2\user\models\User $user */
        /** @var \amnah\yii2\user\models\UserToken $userToken */

        // get user token and check expiration
        $userToken = Yii::$app->getModule("user")->model("UserToken");
        $userToken = $userToken::findByToken($token, $userToken::TYPE_PASSWORD_RESET);
        if (!$userToken) {
            return $this->render('reset', ["invalidToken" => true]);
        }

        // get user and set "reset" scenario
        $success = false;
        $user = Yii::$app->getModule("user")->model("User");
        $user = $user::findOne($userToken->user_id);
        $user->setScenario("reset");

        // load post data and reset user password
        if ($user->load(Yii::$app->request->post()) && $user->save()) {

            // delete userToken and set success = true
            $userToken->delete();
            $success = true;
        }

        // render
        return $this->render('reset', compact("user", "success"));
    }
}