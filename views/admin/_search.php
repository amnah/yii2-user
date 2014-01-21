<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var amnah\yii2\user\models\search\UserSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="user-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'role_id') ?>

		<?= $form->field($model, 'email') ?>

		<?= $form->field($model, 'new_email') ?>

		<?= $form->field($model, 'username') ?>

		<?php // echo $form->field($model, 'password') ?>

		<?php // echo $form->field($model, 'status') ?>

		<?php // echo $form->field($model, 'auth_key') ?>

		<?php // echo $form->field($model, 'create_time') ?>

		<?php // echo $form->field($model, 'update_time') ?>

		<?php // echo $form->field($model, 'ban_time') ?>

		<?php // echo $form->field($model, 'ban_reason') ?>

		<div class="form-group">
			<?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
			<?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
		</div>

	<?php ActiveForm::end(); ?>

</div>
