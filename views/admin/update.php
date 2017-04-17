<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var p2m\users\models\User $user
 * @var p2m\users\models\Profile $profile
 */

$this->title = Yii::t('user', 'Update {modelClass}: ', [
  'modelClass' => 'User',
]) . ' ' . $user->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $user->id, 'url' => ['view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = Yii::t('user', 'Update');
?>
<div class="user-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'user' => $user,
		'profile' => $profile,
	]) ?>

</div>
