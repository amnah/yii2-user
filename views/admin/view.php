<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var amnah\yii2\user\models\User $user
 */

$this->title = $user->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $user->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $user->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php
        $attributes = $user->getAttributes();
        $attributes['full_name'] = $user->profile->full_name;
    ?>
    <?= DetailView::widget([
        'model' => $attributes,
        'attributes' => [
            'id',
            'role_id',
            'status',
            'email:email',
            'new_email:email',
            'username',
            'full_name',
            'password',
            'auth_key',
            'api_key',
            'login_ip',
            'login_time',
            'create_ip',
            'create_time',
            'update_time',
            'ban_time',
            'ban_reason',
        ],
    ]) ?>

</div>
