<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var amnah\yii2\user\Module $module
 * @var amnah\yii2\user\models\forms\LoginEmailForm $loginEmailForm
 */

$module = $this->context->module;

$this->title = Yii::t('user', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-default-login-email">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($flash = Yii::$app->session->getFlash("Login-success")): ?>

        <div class="alert alert-success">
            <p><?= $flash ?></p>
        </div>

    <?php else: ?>

        <p>This will send a link to the email address to log in or register</p>

        <p>These links expire in <?= $module->loginExpireTime ?></p>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-2 control-label'],
            ],
        ]); ?>

        <?= $form->field($loginEmailForm, 'email') ?>
        <?= $form->field($loginEmailForm, 'rememberMe', [
            'template' => "{label}<div class=\"col-lg-offset-2 col-lg-3\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
        ])->checkbox() ?>

        <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10">
                <?= Html::submitButton(Yii::t('user', 'Login'), ['class' => 'btn btn-primary']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

        <?php if (Yii::$app->get("authClientCollection", false)): ?>
            <div class="col-lg-offset-2 col-lg-10">
                <?= yii\authclient\widgets\AuthChoice::widget([
                    'baseAuthUrl' => ['/user/auth/login']
                ]) ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>
