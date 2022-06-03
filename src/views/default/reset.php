<?php

use faro\core\FaroCoreAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var faro\core\user\models\User $user
 * @var bool $success
 * @var bool $invalidToken
 */

FaroCoreAsset::register($this);

$this->title = Yii::t('user', 'Resetear contraseña');
$this->params['breadcrumbs'][] = $this->title;

$directoryFaroAsset = Yii::$app->assetManager->getPublishedUrl('@faro/core/assets');
$imagen = $directoryFaroAsset . "/img/logo_vertical.jpg";
?>
<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"
                             style="background-image: url(<?= $imagen ?>);"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-left">
                                    <h1 class="h4 text-gray-900 mb-4"><?= Html::encode($this->title) ?></h1>
                                </div>

                                <div class="user-default-reset">
                                    
                                    <?php if (!empty($success)): ?>

                                        <div class="alert alert-success">

                                            <p><?= Yii::t("user", "Password has been reset") ?></p>
                                            <p><?= Html::a(Yii::t("user", "Log in here"), ["/user/login"]) ?></p>

                                        </div>

                                    <?php elseif (!empty($invalidToken)): ?>

                                        <div class="alert alert-danger">
                                            <p><?= Yii::t("user", "Invalid token") ?></p>
                                        </div>

                                    <?php else: ?>


                                        <div class="alert alert-warning">
                                            <p><?= Yii::t("user", "Email") ?> [ <?= $user->email ?> ]</p>
                                        </div>

                                        <?php $form = ActiveForm::begin(['id' => 'reset-form']); ?>

                                        <?= $form->field($user, 'newPassword')->passwordInput() ?>
                                        <?= $form->field($user, 'newPasswordConfirm')->passwordInput() ?>
                                        <div class="form-group">
                                            <?= Html::submitButton(Yii::t("user", "Reset"),
                                                ['class' => 'btn btn-primary']) ?>
                                        </div>
                                        <?php ActiveForm::end(); ?>
                                      

                                    <?php endif; ?>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>


