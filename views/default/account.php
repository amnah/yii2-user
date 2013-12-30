<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var amnah\yii2\user\models\User $user
 * @var amnah\yii2\user\models\User $userSuccess
 */
$this->title = 'Account';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-account">
	<h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->getFlash("Account-success")): ?>

        <div class="alert alert-success">Account updated</div>

    <?php elseif (Yii::$app->session->getFlash("Resend-success")): ?>

        <div class="alert alert-success">Confirmation email resent</div>

    <?php elseif (Yii::$app->session->getFlash("Cancel-success")): ?>

        <div class="alert alert-success">Email change cancelled</div>

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

    <?= $form->field($user, 'currentPassword')->passwordInput() ?>

    <hr/>

    <?php if ($this->context->getUserModule()->useEmail): ?>
        <?= $form->field($user, 'email') ?>
    <?php endif; ?>

    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">

            <?php if ($user->new_email): ?>

                <p class="small">Pending email confirmation: [ <?= $user->new_email ?> ]</p>
                <p class="small">
                    <?= Html::a("resend", ["/user/resend-change"]) ?> or <?= Html::a("cancel", ["/user/cancel"]) ?>
                </p>

            <?php elseif ($this->context->getUserModule()->emailConfirmation): ?>

                <p class="small">Changing your email requires email confirmation</p>

            <?php endif; ?>

        </div>
    </div>

    <?php if ($this->context->getUserModule()->useUsername): ?>
        <?= $form->field($user, 'username') ?>
    <?php endif; ?>

    <?= $form->field($user, 'newPassword')->passwordInput() ?>

    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
