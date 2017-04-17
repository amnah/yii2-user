<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'role_id') ?>

	<?= $form->field($model, 'status') ?>

	<?= $form->field($model, 'email') ?>

	<?= $form->field($model, 'username') ?>

	<?php // echo $form->field($model, 'password') ?>

	<?php // echo $form->field($model, 'auth_key') ?>

	<?php // echo $form->field($model, 'access_token') ?>

	<?php // echo $form->field($model, 'logged_in_ip') ?>

	<?php // echo $form->field($model, 'logged_in_at') ?>

	<?php // echo $form->field($model, 'created_ip') ?>

	<?php // echo $form->field($model, 'created_at') ?>

	<?php // echo $form->field($model, 'updated_at') ?>

	<?php // echo $form->field($model, 'banned_at') ?>

	<?php // echo $form->field($model, 'banned_reason') ?>

	<div class="form-group">
		<?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
