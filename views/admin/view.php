<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var amnah\yii2\user\models\User $user
 */

$this->title = $user->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('user', 'Update'), ['update', 'id' => $user->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('user', 'Delete'), ['delete', 'id' => $user->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('user', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $user,
        'attributes' => [
            'id',
            'role_id',
            'status',
            'email:email',
            'username',
            'profile.full_name',
            'password',
            'auth_key',
            'access_token',
            'logged_in_ip',
            [
                'attribute' => 'logged_in_at',
                'value' => call_user_func(function ($model) {
                    if (!$model->logged_in_at) {
                        return null;
                    }

                    if (extension_loaded('intl')) {
                        return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm:ss}', [strtotime($model->logged_in_at)]);
                    }

                    return $model->logged_in_at;
                }, $user)
            ],
            'created_ip',
            [
                'attribute' => 'created_at',
                'value' => call_user_func(function ($model) {
                    if (extension_loaded('intl')) {
                        return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm:ss}', [strtotime($model->created_at)]);
                    }

                    return $model->created_at;
                }, $user)
            ],
            [
                'attribute' => 'updated_at',
                'value' => call_user_func(function ($model) {
                    if (!$model->updated_at) {
                        return null;
                    }

                    if (extension_loaded('intl')) {
                        return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm:ss}', [strtotime($model->updated_at)]);
                    }

                    return $model->updated_at;
                }, $user)
            ],
            [
                'attribute' => 'banned_at',
                'value' => call_user_func(function ($model) {
                    if (!$model->banned_at) {
                        return null;
                    }

                    if (extension_loaded('intl')) {
                        return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm:ss}', [strtotime($model->banned_at)]);
                    }

                    return $model->banned_at;
                }, $user)
            ],
            'banned_reason',
        ],
    ]) ?>

</div>
