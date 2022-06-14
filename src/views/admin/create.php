<?php

use faro\core\widgets\Panel;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var faro\core\user\models\User $user
 * @var faro\core\user\models\Profile $profile
 */

$this->title = Yii::t('user', 'Create {modelClass}', [
  'modelClass' => 'Usuario',
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?php Panel::begin(['header' => 'Agregar usuario']) ?>

    <?= $this->render('_form', [
        'user' => $user,
        'profile' => $profile,
    ]) ?>
    
    <?php Panel::end() ?>

</div>