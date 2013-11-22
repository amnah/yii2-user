<?php

namespace amnah\yii2\user\controllers;

use Yii;
use yii\web\Controller;
use amnah\yii2\user\models\forms\LoginForm;

/**
 * Default controller for User module
 */
class DefaultController extends Controller {

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

        // check if user is already logged in
        if (!Yii::$app->user->isGuest) {
            $this->goHome();
        }

        // set up login form model
        $model = new LoginForm([
            "loginUsername" => $this->module->loginUsername,
            "loginEmail" => $this->module->loginEmail,
        ]);

        // validate data
        if ($model->load($_POST) && $model->validate()) {
            Yii::$app->user->login($model->getUser(), $model->rememberMe ? $this->module->loginDuration : 0);
            return $this->goBack();
        }
        // render view
        else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Display register page
     */
    public function actionRegister() {

    }

    /**
     * Log user out and redirect home
     */
    public function actionLogout() {
        Yii::$app->getUser()->logout();
        $this->goHome();
    }
}