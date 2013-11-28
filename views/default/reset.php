<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var amnah\yii2\user\models\forms\ResetForm $model
 * @var bool $success
 * @var bool $invalidKey
 */
$this->title = 'Reset';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-reset">
	<h1><?= Html::encode($this->title) ?></h1>

    <?php if (!empty($success)): ?>

        <div class="alert alert-success">

            <p>Password reset</p>

            <p><?= Html::a("Log in here", ["/user/login"]) ?></p>

        </div>

    <?php elseif (!empty($invalidKey)): ?>

        <div class="alert alert-danger">Invalid key</div>

	<?php else: ?>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'reset-form']); ?>

                    <?php /*
                    <?= $form->field($model, 'email') ?>
                    */ ?>
                    <?= $form->field($model, 'newPassword')->passwordInput() ?>
                    <?= $form->field($model, 'newPasswordConfirm')->passwordInput() ?>
                    <div class="form-group">
                        <?= Html::submitButton('Reset', ['class' => 'btn btn-primary']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

	<?php endif; ?>
</div>
