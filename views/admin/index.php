<?php

use yii\helpers\Html;
use yii\grid\GridView;
use amnah\yii2\grid\RelatedDataColumn;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var amnah\yii2\user\models\search\UserSearch $searchModel
 */

$this->title = 'Admin';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
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
            'role_id',
            'status',
            'create_time',
            [
                'class' => RelatedDataColumn::className(),
                'attribute' => 'full_name',
                'related' => 'profile',
                'label' => 'Full Name',
            ],
//            'new_email:email',
//            'password',
//            'auth_key',
//            'update_time',
//            'ban_time',
//            'ban_reason',

			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>

</div>
