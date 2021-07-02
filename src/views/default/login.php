<?php

use faro\core\FaroCoreAsset;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var faro\core\user\models\forms\LoginForm $model
 */

FaroCoreAsset::register($this);

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;

$asset = FaroCoreAsset::register($this);
$imagen = $asset->baseUrl . "/img/logo_vertical.jpg";

?>

<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image" style="background-image: url(<?= $imagen ?>);"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Bienvenido!</h1>
                                </div>
                                <?php $form = ActiveForm::begin([
                                    'id' => 'login-form',
                                    'layout' => 'horizontal',
                                    'options' => ['class' => 'user'],
                                    'fieldConfig' => [
                                        'template' => "{label}\n<div class=\"col-12\">{input}</div>\n<div class=\"col-12\">{error}</div>",
                                        'labelOptions' => ['class' => 'col-lg-1 control-label'],
                                    ],
                                ]); ?>


                                <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'Email', 'class' => 'form-control form-control-user'])->label(false) ?>

                                <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password', 'class' => 'form-control form-control-user'])->label(false) ?>

                                <?= $form->field($model, 'rememberMe')->checkbox([
                                    'template' => "<div class=\"col-12\">{input} {label}</div>\n<div class=\"col-12\">{error}</div>",
                                ]) ?>

                                <div class="form-group">
                                    <div class="col-12">
                                        <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-user btn-block', 'name' => 'login-button']) ?>
                                    </div>
                                    
                                    <div class="col-12">
                                        <?= Html::a(Yii::t("user", "Register"), ["/user/register"]) ?> /
                                        <?= Html::a(Yii::t("user", "Forgot password") . "?", ["/user/forgot"]) ?> /
                                        <?= Html::a(Yii::t("user", "Resend confirmation email"), ["/user/resend"]) ?>
                                    </div>
                                </div>
                                
                                

                                <!-- 
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Enter Email Address...">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="customCheck">
                                        <label class="custom-control-label" for="customCheck">Remember Me</label>
                                    </div>
                                </div>
                                <a href="index.html" class="btn btn-primary btn-user btn-block">
                                    Login
                                </a>
                               <hr>
                                <a href="index.html" class="btn btn-google btn-user btn-block">
                                    <i class="fab fa-google fa-fw"></i> Login with Google
                                </a>
                                <a href="index.html" class="btn btn-facebook btn-user btn-block">
                                    <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                                </a>-->
                                <?php ActiveForm::end(); ?>
                                <!--                                <hr>-->
                                <!--                                <div class="text-center">-->
                                <!--                                    <a class="small" href="forgot-password.html">Forgot Password?</a>-->
                                <!--                                </div>-->
                                <!--                                <div class="text-center">-->
                                <!--                                    <a class="small" href="register.html">Create an Account!</a>-->
                                <!--                                </div>-->

                                <?php if (Yii::$app->get("authClientCollection", false)): ?>
                                    <div class="col-lg-offset-2 col-lg-10">
                                        <?= yii\authclient\widgets\AuthChoice::widget([
                                            'baseAuthUrl' => ['/user/auth/login']
                                        ]) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>


