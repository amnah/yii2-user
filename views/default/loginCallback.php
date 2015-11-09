<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var amnah\yii2\user\Module $module
 * @var amnah\yii2\user\models\User $user
 * @var amnah\yii2\user\models\Profile $profile
 * @var amnah\yii2\user\models\UserToken $userToken
 */

$module = $this->context->module;

$this->title = Yii::t('user', 'Register');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-default-login-callback">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (!$userToken): ?>

        <div class="alert alert-danger">
            <p><?= Yii::t("user", "Invalid Token") ?></p>
        </div>

    <?php else: ?>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-2 control-label'],
            ],
        ]); ?>

        <?= $form->field($user, 'email')->textInput(['disabled' => true]) ?>
        <?php if ($module->useUsername): ?>
            <?= $form->field($user, 'username') ?>
        <?php endif; ?>
        <?= $form->field($profile, 'full_name') ?>

        <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10">
                <?= Html::submitButton(Yii::t('user', 'Register'), ['class' => 'btn btn-primary']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    <?php endif; ?>
</div>
