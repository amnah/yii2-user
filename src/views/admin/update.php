<?php

use faro\core\widgets\Panel;
use yii\bootstrap4\Alert;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var faro\core\user\models\User $user
 * @var faro\core\user\models\Profile $profile
 */

$this->title = "Actualizar usuario " . $user->profile->full_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administración'), 'url' => ['/faro/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $user->profile->full_name, 'url' => ['view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = Yii::t('user', 'Update');
?>
<div class="user-update">

    <?php Panel::begin(["header" => "Editar usuario"]) ?>

    <?= $this->render('_form', [
        'user' => $user,
        'profile' => $profile,
    ]) ?>
    
    <?php Panel::end() ?>


    <?php Panel::begin(["header" => "Eliminar campaña", "margenTop" => true]) ?>

    <p>Para eliminar este usuario ingresá desde acá. Si creés que en un futuro puede volver a ser
    necesario te sugerimos que lo desactives.</p>

    <?= Alert::widget([
        "body" => "<i class='fas fa-exclamation-triangle mr-2'></i> Esta acción no puede deshacerse",
        "closeButton" => false,
        "options" => ["class" => "alert-danger"]
    ]) ?>

    <?=
    Html::a("Eliminar usuario", ['delete', 'id' => $user->id], [
        'class' => 'btn btn-danger btn-block shadow-sm d-sm-inline-block',
        'data' => [
            'confirm' => Yii::t('app', 'Esta seguro que desea eliminar este usuario?'),
            'method' => 'post',
        ],
    ])
    ?>

    <?php Panel::end() ?>

</div>
