<?php

use amnah\yii2\user\models\User;
use amnah\yii2\user\models\Profile;
use amnah\yii2\user\models\Userkey;
/**
 * @var User $user
 * @var Profile $profile
 * @var Userkey $userkey
 * @var string $subject
 */
?>

<h3><?= $subject ?></h3>

<p>Please confirm your email address by clicking the link below:</p>

<p><?= Yii::$app->urlManager->createAbsoluteUrl("user/confirm", ["key" => $userkey->key]); ?></p>