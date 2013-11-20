<?php

namespace amnah\yii2\user\controllers;

use Yii;
use yii\web\Controller;
use amnah\yii2\user\models\forms\LoginForm;

class DefaultController extends Controller
{
	public function actionIndex()
	{
		return $this->render('index');
	}

    public function actionLogin() {

        if (!Yii::$app->user->isGuest) {
            $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load($_POST) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout() {

    }
}