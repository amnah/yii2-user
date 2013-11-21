<?php

namespace amnah\yii2\user\controllers;

use Yii;
use yii\web\Controller;
use amnah\yii2\user\models\forms\LoginForm;

/**
 * Default controller for User module
 *
 * @author amnah <amnah.dev@gmail.com>
 */
class DefaultController extends Controller {

    /**
     * Displays index
     */
    public function actionIndex() {
        return $this->render('index');
    }

    /**
     * Displays login page
     */
    public function actionLogin() {

        // check if user is already logged in
        if (!Yii::$app->user->isGuest) {
            $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load($_POST) && $model->login()) {
            return $this->goBack();
        }
        else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays register page
     */
    public function actionRegister() {

    }

    /**
     * Logs user out and redirect home
     */
    public function actionLogout() {
        Yii::$app->getUser()->logout();
        $this->goHome();
    }
}