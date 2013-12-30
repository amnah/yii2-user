<?php

use yii\helpers\Html;
use yii\grid\GridView;
$user = $this->context->getUserModule()->model("User");
$role = $this->context->getUserModule()->model("Role");

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var amnah\yii2\user\models\User $user
 * @var amnah\yii2\user\models\Role $role
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
                'attribute' => 'full_name',
                'label' => 'Full Name',
                'value' => function($model, $index, $dataColumn) {
                        return $model->profile->full_name;
                    }
            ],
            [
                'attribute' => 'status',
                'label' => 'Status',
                'filter' => $user::statusDropdown(),
                'value' => function($model, $index, $dataColumn) use ($user) {
                    $statusDropdown = $user::statusDropdown();
                    return $statusDropdown[$model->status];
                },
            ],
            [
                'attribute' => 'role_id',
                'label' => 'Role',
                'filter' => $role::dropdown(),
                'value' => function($model, $index, $dataColumn) use ($role) {
                    $roleDropdown = $role::dropdown();
                    return $roleDropdown[$model->role_id];
                },
            ],
            'create_time',
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
