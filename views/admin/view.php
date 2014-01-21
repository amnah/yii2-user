<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var amnah\yii2\user\models\User $model
 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?php echo Html::a('Delete', ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data-confirm' => Yii::t('app', 'Are you sure to delete this item?'),
			'data-method' => 'post',
		]); ?>
	</p>

    <?php
        $attributes = $model->getAttributes();
        $attributes["full_name"] = $model->profile->full_name;
    ?>
	<?php echo DetailView::widget([
		'model' => $attributes,
		'attributes' => [
			'id',
			'role_id',
			'email:email',
			'new_email:email',
			'username',
            'full_name',
			'status',
			'auth_key',
			'created_at',
			'updated_at',
			'ban_time',
			'ban_reason',
		],
	]); ?>

</div>
