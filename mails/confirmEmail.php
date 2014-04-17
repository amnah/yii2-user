<?php

use yii\helpers\Url;

/**
 * @var string $subject
 * @var User $user
 * @var Profile $profile
 * @var Userkey $userkey
 */
?>

<h3><?= $subject ?></h3>

<p>Please confirm your email address by clicking the link below:</p>

<p><?= Url::toRoute(["/user/confirm", "key" => $userkey->key], true); ?></p>