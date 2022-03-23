<?php

use faro\core\components\ControlUsuarios;
use faro\core\widgets\AccionesLayoutWidget;
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

if (ControlUsuarios::esAdmin()) {
    AccionesLayoutWidget::agregarBoton(
        \yii\bootstrap4\Html::a("<i class='fas fa-edit'></i> Editar usuario", ['update', "id" => $user->id], ["class" => "dropdown-item"])
    );
}
?>

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
