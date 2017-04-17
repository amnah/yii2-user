<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Profile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="profile-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'user_id')->textInput() ?>

	<?= $form->field($model, 'givenName')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'familyName')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'preferredName')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'fullName')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'phone1')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'phone2')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'address1')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'address2')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'locality')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'state')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'postcode')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'timezone')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'created_at')->textInput() ?>

	<?= $form->field($model, 'created_by')->textInput() ?>

	<?= $form->field($model, 'updated_at')->textInput() ?>

	<?= $form->field($model, 'updated_by')->textInput() ?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
