<?php

use yii\helpers\Url;

/**
 * @var string $subject
 * @var User $user
 * @var Userkey $userkey
 */
?>

<h3><?= $subject ?></h3>

<p>Please use this link to reset your password:</p>

<p><?= Url::toRoute(["/user/reset", "key" => $userkey->key], true); ?></p>
