<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var string $subject
 * @var \p2m\users\models\User $user
 * @var \p2m\users\models\UserToken $userToken
 */

$url = Url::toRoute(["/user/login-callback", "token" => $userToken->token], true);
?>

<h3><?= $subject ?></h3>

<p><?= Html::a($url, $url) ?></p>