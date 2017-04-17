<?php
/**
 * _search.php
 *
 * @copyright Copyright &copy; Pedro Plowman, 2017
 * @author Pedro Plowman
 * @link https://github.com/p2made
 * @license MIT
 *
 * @package p2made/yii2-p2y2-users
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProfileSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="profile-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'user_id') ?>

	<?= $form->field($model, 'givenName') ?>

	<?= $form->field($model, 'familyName') ?>

	<?= $form->field($model, 'preferredName') ?>

	<?php // echo $form->field($model, 'fullName') ?>

	<?php // echo $form->field($model, 'phone1') ?>

	<?php // echo $form->field($model, 'phone2') ?>

	<?php // echo $form->field($model, 'address1') ?>

	<?php // echo $form->field($model, 'address2') ?>

	<?php // echo $form->field($model, 'locality') ?>

	<?php // echo $form->field($model, 'state') ?>

	<?php // echo $form->field($model, 'postcode') ?>

	<?php // echo $form->field($model, 'country') ?>

	<?php // echo $form->field($model, 'timezone') ?>

	<?php // echo $form->field($model, 'created_at') ?>

	<?php // echo $form->field($model, 'created_by') ?>

	<?php // echo $form->field($model, 'updated_at') ?>

	<?php // echo $form->field($model, 'updated_by') ?>

	<div class="form-group">
		<?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
