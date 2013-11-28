<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var amnah\yii2\user\models\forms\ForgotForm $model
 */
$this->title = 'Forgot';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-forgot">
	<h1><?= Html::encode($this->title) ?></h1>

	<?php if (Yii::$app->session->getFlash('Forgot-success')): ?>

        <div class="alert alert-success">
            Instructions to reset your password have been sent
        </div>

	<?php else: ?>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'forgot-form']); ?>
                    <?= $form->field($model, 'email') ?>
                    <div class="form-group">
                        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

	<?php endif; ?>
</div>
