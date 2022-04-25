<?php

use faro\core\components\ControlUsuarios;
use faro\core\components\FaroGridView;
use faro\core\widgets\AccionesLayoutWidget;
use faro\core\widgets\Panel;
use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var faro\core\user\Module $module
 * @var faro\core\user\models\search\UserSearch $searchModel
 * @var faro\core\user\models\User $user
 * @var faro\core\user\models\Role $role
 */

$module = $this->context->module;
$user = $module->model("User");
$role = $module->model("Role");

$this->title = Yii::t('user', 'Users');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administración'), 'url' => ['/faro/admin']];
$this->params['breadcrumbs'][] = $this->title;

$this->params["navbar_menu_selected"] = "administracion";

if (ControlUsuarios::esAdmin()) {
    AccionesLayoutWidget::agregarBoton(
        \yii\bootstrap4\Html::a("<i class='fas fa-plus-circle'></i> Nuevo usuario", ['create'],
            ["class" => "dropdown-item"])
    );
}
?>

<div class="user-index">

    <?php Panel::begin(['header' => 'Listado de usuarios']) ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php \yii\widgets\Pjax::begin(); ?>
    <?= FaroGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'headerContainer' => ['class' => ''],
        'striped' => false,
        "bordered" => false,
        "layout" => "{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'role_id',
                'label' => Yii::t('user', 'Role'),
                'filter' => $role::dropdown(),
                'value' => function ($model, $index, $dataColumn) use ($role) {
                    $roleDropdown = $role::dropdown();
                    return $roleDropdown[$model->role_id];
                },
            ],
            'profile.full_name' => [
                "attribute" => "profile.full_name",
                "format" => "raw",
                "value" => function ($model) {
                    return \yii\bootstrap4\Html::a($model->profile->full_name,
                        ["view", "id" => $model->id]);
                }
            ],
            'email:email',
            [
                'attribute' => 'status',
                'label' => Yii::t('user', 'Status'),
                'filter' => $user::statusDropdown(),
                'value' => function ($model, $index, $dataColumn) use ($user) {
                    $statusDropdown = $user::statusDropdown();
                    return $statusDropdown[$model->status];
                },
            ],

            'profile.timezone',
            'created_at:relativeTime',
            // 'username',
            // 'password',
            // 'auth_key',
            // 'access_token',
            // 'logged_in_ip',
            // 'logged_in_at',
            // 'created_ip',
            // 'updated_at',
            // 'banned_at',
            // 'banned_reason',
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>

    <?php Panel::end() ?>
</div>
