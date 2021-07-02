<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var string $subject
 * @var \faro\core\user\models\User $user
 * @var \faro\core\user\models\UserToken $userToken
 */

$url = Url::toRoute(["/user/reset", "token" => $userToken->token], true);
?>

<h3><?= $subject ?></h3>

<p><?= Yii::t("user", "Please use this link to reset your password:") ?></p>

<p><?= Html::a($url, $url) ?></p>
