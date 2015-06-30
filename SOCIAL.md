Yii 2 User - Social authentication
=========

Yii 2 User supports social authentication. It is based on
[yii2-authclient](https://github.com/yiisoft/yii2-authclient), so it works very easily with
the built-in clients[](https://github.com/yiisoft/yii2-authclient#predefined-auth-clients).
In addition, this module also supports social authentication via
[reddit](components/RedditAuth.php).

In general, there are two main ways to use social authentication:

* [Connecting social accounts to existing user account](#connecting-social-accounts)
* [Registration/login using social auth](#registrationlogin-using-social-auth)

Both ways are supported in this module, but take a bit of extra work to set up.

## Setup

First, we'll need to set up the social accounts by adding the components config:

**Note: you can use any of the [predefined]
(http://www.yiiframework.com/doc-2.0/ext-authclient-index.html#predefined-auth-clients) 
auth clients**

```php
// @app/config/web.php
'components' => [
    'authClientCollection' => [
        'class' => 'yii\authclient\Collection',
        'clients' => [
            'facebook' => [
                'class' => 'yii\authclient\clients\Facebook',
                'clientId' => 'xxxxxxxxxx',
                'clientSecret' => 'yyyyyyyyyy',
                'scope' => 'email',
            ],
            'twitter' => [
                'class' => 'yii\authclient\clients\Twitter',
                'consumerKey' => 'xxxxxxxxxx',
                'consumerSecret' => 'yyyyyyyyyy',
            ],
            'google' => [
                'class' => 'yii\authclient\clients\GoogleOAuth',
                'clientId' => 'xxxxxxxxxx',
                'clientSecret' => 'yyyyyyyyyy',
            ],
            'reddit' => [
                'class' => 'amnah\yii2\user\components\RedditAuth',
                'clientId' => 'xxxxxxxxxx',
                'clientSecret' => 'yyyyyyyyyy',
                'scope' => 'identity', // comma separated string, NO SPACES
                 // @see https://github.com/reddit/reddit/wiki/OAuth2#authorization
            ],
            'vkontakte' => [
                'class' => 'yii\authclient\clients\VKontakte',
                'clientId' => 'xxxxxxxxxx',
                'clientSecret' => 'yyyyyyyyyy', // @deploy - set in main-local.php
                'scope' => '4194304', // 4194304 in vk API bit masks means 'email'
            ],
            // any other social auth
        ],
    ],
],
```

These are the four clients that are built-in and work right out the box.

## Connecting social accounts

Let's start with the easy part: connecting social accounts. Because the user's account has
already been created, we don't want to change any of the account/profile information that
the user has already submitted. Instead, we only create a link by adding a ```UserAuth```
record. *Note that users can log in using their social accounts from then on*

In order to allow users to connect their account, we can simply add in a widget to the desired
view file.

```php
// view file
// note that this won't show anything unless the "authClientCollection" component is set up
<?= yii\authclient\widgets\AuthChoice::widget([
    'baseAuthUrl' => ['/user/auth/connect']
]) ?>
```

To further customize this process, [extend]
(https://github.com/amnah/yii2-user/blob/master/README.md#how-do-i-extend-this-package)
 and override ```AuthController::actions()``` and ```AuthController::connectCallback()```.

## Registration/login using social auth

This is the tricky part because there are several cases that we need to consider:

1. If the ```UserAuth``` has been registered or connected before, then we simply log the user in
2. If not, then we will need to register the user by creating a ```$user``` and ```$profile```
    * If the social account's email already exists in the database, then we link the social
    account to that user via ```UserAuth```. If not, then we create a new user
    * If the social account's username already exists in the database, then we CANNOT make the
    same assumption - we must create a new user. But because the username is already taken, we
    use a ```$fallbackUsername```, which by default is client name + id (eg, "facebook_12142124124")

**NOTE: Some social accounts do not provide the user's email address, eg, twitter and reddit**

Of course, the attributes map from social account to user account depends on the project's
requirements. Let's take a look at the basic implementation for facebook:

```php
/**
 * Set info for facebook registration
 *
 * @param array $attributes
 * @return array [$user, $profile]
 */
protected function setInfoFacebook($attributes)
{
    /** @var \amnah\yii2\user\models\User    $user */
    /** @var \amnah\yii2\user\models\Profile $profile */
    $user = Yii::$app->getModule("user")->model("User");
    $profile = Yii::$app->getModule("user")->model("Profile");

    $user->email = $attributes["email"];
    $user->username = $attributes["username"];
    $profile->full_name = $attributes["name"];

    return [$user, $profile];
}
```

Note the function name - it should follow the convention ```function setInfo[clientName]```. To
customize, extend and override.

And similarly to connecting, we can add a widget to the desired view file:

```php
// view file
// note that this won't show anything unless the "authClientCollection" component is set up
<?= yii\authclient\widgets\AuthChoice::widget([
    'baseAuthUrl' => ['/user/auth/login'] // "login" instead of "connect"
]) ?>
```

## Reddit

Reddit authentication is supported by default, but is a little strict. Specifically, we need to
take care and set the app's *redirect uri* setting properly.

* http://localhost/yii2-app/web/user/auth/login?authclient=reddit

## Google

Unfortunately, Google has shut down its OpenID authentication. We'll need to use GoogleOAuth
instead, so here are the steps to getting it work:

1. Create a project at https://console.developers.google.com/project
2. Apis & Auth -> Credentials -> Create a new Client ID
3. Apis & Auth -> APIs -> Enable **Contacts API** and **Google+ API**
4. If you still get a 403 error, try restarting your computer to refresh the token
(restarting the browser didn't work for me)

See [issue](https://github.com/amnah/yii2-user/issues/25) for more details.