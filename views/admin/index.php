<?php

use yii\helpers\Html;
use yii\grid\GridView;
use amnah\yii2\user\models\User;
use amnah\yii2\user\models\Role;
//use amnah\yii2\grid\RelatedDataColumn;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var amnah\yii2\user\models\search\UserSearch $searchModel
 */

$this->title = 'Users';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/user/admin']];
?>
<div class="user-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

	<?php echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'id',
            'email:email',
            'username',
            [
                'attribute' => 'role_id',
                'label' => 'Role',
                'filter' => Role::dropdown(),
                'value' => function($model, $index, $dataColumn) {
                    $roleDropdown = Role::dropdown();
                    return $roleDropdown[$model->role_id];
                },
            ],
            [
                'attribute' => 'status',
                'label' => 'Status',
                'filter' => User::statusDropdown(),
                'value' => function($model, $index, $dataColumn) {
                    $statusDropdown = User::statusDropdown();
                    return $statusDropdown[$model->status];
                },
            ],
            'create_time',
            [
                'attribute' => 'full_name',
                'label' => 'Full Name',
                'value' => function($model, $index, $dataColumn) {
                    return $model->profile->full_name;
                }
            ],
            /*
            'new_email:email',
            'password',
            'auth_key',
            'update_time',
            'ban_time',
            'ban_reason',
            */

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>

</div>
