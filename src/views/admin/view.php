<?php

use faro\core\widgets\Panel;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var faro\core\user\models\User $user
 */

$this->title = $user->email;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

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


<div class="user-view">

    <?php Panel::begin(['header' => 'Ver usuario']) ?>
    
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
            'logged_in_at',
            'created_ip',
            'created_at',
            'updated_at',
            'banned_at',
            'banned_reason',
        ],
    ]) ?>
    
    <?php Panel::end() ?>

</div>
