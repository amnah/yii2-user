<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var amnah\yii2\user\models\forms\Resend $model
 */
$this->title = 'Resend';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-resend">
	<h1><?= Html::encode($this->title) ?></h1>

	<?php if (Yii::$app->session->getFlash('Resend-success')): ?>

        <div class="alert alert-success">
            Email confirmation resent
        </div>

	<?php else: ?>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'resend-form']); ?>
                    <?= $form->field($model, 'email') ?>
                    <div class="form-group">
                        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

	<?php endif; ?>
</div>
