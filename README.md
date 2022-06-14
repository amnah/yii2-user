Yii 2 User
=========

Yii 2 User - User authentication module

## New version released 01/31/2016

This release contains a few small updates and bug fixes. Most notably, I've changed
`LoginForm.username` to `LoginForm.email` and added a timezone field to `app\models\Profile`
(thanks [mnglkhn](https://github.com/amnah/yii2-user/pull/127))

If there are any issues, [let me know](https://github.com/amnah/yii2-user/issues) and I'll get
to it asap.

## Demo

* [Demo](https://yii2.amnah.dev/user)

## Features

* Quick setup - works out of the box so you can see what it does
* Easily [extendable](#how-do-i-extend-this-package)
* Registration using email and/or username
* Login using email and/or username
* Login/register via email (enter email > get link in inbox > click link to login/register)
* Email confirmation (+ resend functionality)
* [Social authentication](SOCIAL.md) (facebook, twitter, google, linkedin, reddit, vkontakte)
* Account page
    * Updates email, username, and password
    * Requires current password
* Profile page
    * Lists custom fields for users, e.g., *full_name*
* Password recovery
* Admin crud via GridView

## Installation

* Install [Yii 2](http://www.yiiframework.com/download) using your preferred method
* Install package via [composer](http://getcomposer.org/download/) ```"amnah/yii2-user": "^5.0"```
* Update config file *config/web.php* and *config/db.php*

```php
// app/config/web.php
return [
    'components' => [
        // NOTE: in the yii2-advanced-app, the user component should be updated in
        // 'frontend/config/main.php' and/or 'backend/config/main.php' (OR you can add it
        // to 'common/config' if you remove it from frontend/backend)
        'user' => [
            'class' => 'faro\core\user\components\User',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
            'messageConfig' => [
                'from' => ['admin@website.com' => 'Admin'], // this is needed for sending emails
                'charset' => 'UTF-8',
            ]
        ],
    ],
    'modules' => [
        'user' => [
            'class' => 'faro\core\user\Module',
            // set custom module properties here ...
        ],
    ],
];
// app/config/db.php
return [
    'class' => 'yii\db\Connection',
    // set up db info
];
```

* Run migration file
    * ```php yii migrate --migrationPath=@vendor/amnah/yii2-user/migrations```
* Go to your application in your browser
    * ```http://localhost/pathtoapp/web/user``` 
    * **note:** go to `user/login` instead of `site/login`
* Log in as admin using ```neo/neo``` (change it!)
* Set up [module properties](Module.php#L14) as desired
* *Optional* - Update the nav links in your main layout *app/views/layouts/main.php*

```php
// app/views/layouts/main.php
<?php
'items' => [
    ['label' => 'Home', 'url' => ['/site/index']],
    ['label' => 'About', 'url' => ['/site/about']],
    ['label' => 'Contact', 'url' => ['/site/contact']],
    ['label' => 'User', 'url' => ['/user']],
    Yii::$app->user->isGuest ?
        ['label' => 'Login', 'url' => ['/user/login']] : // or ['/user/login-email']
        ['label' => 'Logout (' . Yii::$app->user->displayName . ')',
            'url' => ['/user/logout'],
            'linkOptions' => ['data-method' => 'post']],
],
```

## Development Notes

### How do I check user permissions?

This package contains a custom permissions system. Every user has a role, and that role has
permissions in the form of database columns. It should follow the format:
```can_{permission name}```.

For example, the ```role``` table has a column named ```can_admin``` by default. To check if
the user can perform admin actions:

```php
if (!Yii::$app->user->can("admin")) {
    throw new HttpException(403, 'You are not allowed to perform this action.');
}
// --- or ----
$user = User::findOne(1);
if ($user->can("admin")) {
    // do something
};
```

Add more database columns for permissions as needed. If you need something more powerful, look
into setting up
[RBAC] (https://github.com/yiisoft/yii2/blob/master/docs/guide/security-authorization.md#role-based-access-control-rbac).

**Note:** If you set up an ```authManager``` component for RBAC, then ```Yii::$app->user->can()```
will use that instead of this module's custom ```role``` table.

### How do I add captcha to the forms?

Check out this great 3-step
[guide](https://github.com/dektrium/yii2-user/blob/master/docs/adding-captcha.md) by
[dektrium](https://github.com/dektrium). (Please note that the scenarios for the validation 
rules will depend on your project requirements.)

### How do I add i18n?

```php
// app/config/web.php
return [
    'components' => [
        'i18n' => [
            'translations' => [
                'user' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages', // example: @app/messages/fr/user.php
                ]
            ],
        ],
    ],
];
```

### How do I extend this package?

You can extend the classes directly. Depending on which ones you need, set the proper config
property:

```php
// app/config/web.php
'components' => [
    'user' => [
        'class' => 'app\components\MyUser',
        'identityClass' => 'app\models\MyUser',
    ],
],
'modules' => [
    'user' => [
        'class' => 'app\modules\MyModule',
        'controllerMap' => [
            'default' => 'app\controllers\MyDefaultController',
        ],
        'modelClasses'  => [
            'User' => 'app\models\MyUser', // note: don't forget component user::identityClass above
            'Profile' => 'app\models\MyProfile',
        ],
        'emailViewPath' => '@app/mail/user', // example: @app/mail/user/confirmEmail.php
    ],
],
```

For view files, you can use the ```theme``` component.

```php
// app/config/web.php
'components' => [
    'view' => [
        'theme' => [
            'pathMap' => [
                '@vendor/amnah/yii2-user/views' => '@app/views/user', // example: @app/views/user/default/login.php
            ],
        ],
    ],
],
```

### I need more control. Can I just extend the whole thing?

You can always fork the package and modify it as needed.

Or, if you want, you can integrate the package directly into your app by copying the files. 
This would make it more difficult to get updates, but it also guarantees that your app won't 
break after running ```composer update```.

To do so, you can use the helper command ```CopyController```.

* Add the module to your *config/console.php* to gain access to the command 
(**Note: this is CONSOLE config**)

```php
// app/config/console.php
'modules' => [
    'user' => [
        'class' => 'faro\core\user\Module',
    ],
],
```

* Use the ```php yii user/copy``` command. For a [basic app](https://github.com/yiisoft/yii2-app-basic), 
you can call the default command without any options

```
php yii user/copy --from=@vendor/amnah/yii2-user --to=@app/modules/user --namespace=app\\modules\\user
```

* Update config to point to your new package

```php
// app/config/web.php + app/config/console.php
'modules' => [
    'user' => [
        'class' => 'app\modules\user\Module',
    ],
],
```

**Alternatively,** you can do this manually. Just copy/paste the files wherever you'd like and
change the namespaces in the files. Replace ```faro\core\user``` with ```app\modules\user```.

## Todo
* Tests
* Issues/requests? Submit a [github issue](https://github.com/amnah/yii2-user/issues)
