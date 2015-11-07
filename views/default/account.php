<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var amnah\yii2\user\Module $module
 * @var amnah\yii2\user\models\User $user
 * @var amnah\yii2\user\models\UserToken $userToken
 */

$module = $this->context->module;

$this->title = Yii::t('user', 'Account');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-default-account">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($flash = Yii::$app->session->getFlash("Account-success")): ?>

        <div class="alert alert-success">
            <p><?= $flash ?></p>
        </div>

    <?php elseif ($flash = Yii::$app->session->getFlash("Resend-success")): ?>

        <div class="alert alert-success">
            <p><?= $flash ?></p>
        </div>

    <?php elseif ($flash = Yii::$app->session->getFlash("Cancel-success")): ?>

        <div class="alert alert-success">
            <p><?= $flash ?></p>
        </div>

    <?php endif; ?>

    <?php $form = ActiveForm::begin([
        'id' => 'account-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label'],
        ],
        'enableAjaxValidation' => true,
    ]); ?>

    <?php if ($user->password): ?>
        <?= $form->field($user, 'currentPassword')->passwordInput() ?>
    <?php endif ?>

    <hr/>

    <?php if ($module->useEmail): ?>
        <?= $form->field($user, 'email') ?>
    <?php endif; ?>

    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">

            <?php if (!empty($userToken->data)): ?>

                <p class="small"><?= Yii::t('user', "Pending email confirmation: [ {newEmail} ]", ["newEmail" => $userToken->data]) ?></p>
                <p class="small">
                    <?= Html::a(Yii::t("user", "Resend"), ["/user/resend-change"]) ?> / <?= Html::a(Yii::t("user", "Cancel"), ["/user/cancel"]) ?>
                </p>

            <?php elseif ($module->emailConfirmation): ?>

                <p class="small"><?= Yii::t('user', 'Changing your email requires email confirmation') ?></p>

            <?php endif; ?>

        </div>
    </div>

    <?php if ($module->useUsername): ?>
        <?= $form->field($user, 'username') ?>
    <?php endif; ?>

    <?= $form->field($user, 'newPassword')->passwordInput() ?>

    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <?= Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <?php foreach ($user->userAuths as $userAuth): ?>
                <p>Linked Social Account: <?= ucfirst($userAuth->provider) ?> / <?= $userAuth->provider_id ?></p>
            <?php endforeach; ?>
        </div>
    </div>

</div>