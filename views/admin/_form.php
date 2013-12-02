<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var amnah\yii2\user\models\User $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="user-form">

	<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

		<?= $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>

		<?= $form->field($model, 'newPassword'); ?>

		<?= $form->field($model, 'role_id'); ?>

		<?= $form->field($model, 'status'); ?>

        <?php // use checkbox for ban_time ?>
        <?= Html::activeLabel($model, 'ban_time', ['label' => 'Banned']); ?>
        <?= Html::activeCheckbox($model, 'ban_time'); ?>
        <?= Html::error($model, 'ban_time'); ?>

		<?= $form->field($model, 'ban_reason'); ?>

		<div class="form-group">
			<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>

	<?php ActiveForm::end(); ?>

</div>
